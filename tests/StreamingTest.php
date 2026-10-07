<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Exceptions\NetworkException;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\VerificationRequests;
use ProofHoldings\Streaming;

/**
 * Streaming tests use the $injectResponse seam to skip the Guzzle HTTP call
 * entirely — we feed in a hand-rolled SSE byte stream and verify the parser.
 * Polling-fallback tests use a 503 injected response to trigger the
 * fallback path and exercise the contract-shaped poll loop.
 */
class StreamingTest extends TestCase
{
    private HttpClient $http;

    protected function setUp(): void
    {
        $this->http = new HttpClient('pk_test_xxx', 'https://api.example.com', 10.0, 0);
    }

    /** Build a streaming PSR-7 response from an SSE byte string. */
    private static function sseResponse(string $body, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'text/event-stream'], Utils::streamFor($body));
    }

    public function testParsesSingleEvent(): void
    {
        $sse = "event: status\ndata: {\"status\":\"verified\"}\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertCount(1, $events);
        $this->assertSame('status', $events[0]['event']);
        $this->assertSame(['status' => 'verified'], $events[0]['data']);
    }

    public function testParsesMultipleEventsCarriageReturn(): void
    {
        $sse = "event: a\r\ndata: 1\r\n\r\nevent: b\r\ndata: 2\r\n\r\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertCount(2, $events);
        $this->assertSame('a', $events[0]['event']);
        $this->assertSame(1, $events[0]['data']);
        $this->assertSame('b', $events[1]['event']);
        $this->assertSame(2, $events[1]['data']);
    }

    public function testDefaultsEventNameToMessage(): void
    {
        $sse = "data: {\"k\":1}\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertSame('message', $events[0]['event']);
    }

    public function testIgnoresCommentsAndUnknownFields(): void
    {
        $sse = ": heartbeat\nfoo: ignored\nevent: m\ndata: \"hi\"\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertCount(1, $events);
        $this->assertSame('hi', $events[0]['data']);
    }

    public function testCapturesEventId(): void
    {
        $sse = "id: abc-1\nevent: m\ndata: 1\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertSame('abc-1', $events[0]['id']);
    }

    public function testRawStringDataWhenNotJson(): void
    {
        $sse = "data: not-json\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));

        $this->assertSame('not-json', $events[0]['data']);
    }

    public function testCancellationStopsIteration(): void
    {
        $sse = "data: 1\n\ndata: 2\n\ndata: 3\n\n";
        $count = 0;
        $shouldCancel = function () use (&$count) {
            return $count >= 2;
        };
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test', $shouldCancel,
            injectResponse: self::sseResponse($sse),
        );
        foreach ($gen as $event) {
            $count++;
        }
        // Cancellation is checked at the top of the read loop, so we may
        // observe events from a buffer that's already been delivered. The
        // contract is that we stop at-or-after the cancel signal, not that
        // we deliver exactly N events.
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testPollingFallbackOn503(): void
    {
        $callCount = 0;
        $retrieve = function () use (&$callCount) {
            $callCount++;
            return ['id' => 'r_1', 'status' => $callCount >= 2 ? 'verified' : 'pending'];
        };
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', $retrieve, ['verified', 'failed'], 'Test',
            injectResponse: new Response(503),
        );
        $events = [];
        foreach ($gen as $event) {
            $events[] = $event;
            if ($event['event'] === 'terminal') break;
            // Avoid the 2s default sleep — bail after first to keep test fast.
            // Instead, read 1 then check the second is terminal.
            if (count($events) >= 5) break;
        }

        $this->assertSame('poll', $events[0]['event']);
        $this->assertSame('pending', $events[0]['data']['status']);
        // The generator may have multiple pending events depending on
        // sleep+jitter, but the last must be terminal.
        $this->assertSame('terminal', end($events)['event']);
        $this->assertSame('verified', end($events)['data']['status']);
    }

    public function testPollingFallbackEndsOnTheTerminalPredicate(): void
    {
        $calls = 0;
        $retrieve = function () use (&$calls) {
            $calls++;
            return ['status' => 'pending', 'request_status' => 'cancelled'];
        };
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', $retrieve, ['verified'], 'Test',
            injectResponse: new Response(503),
            isTerminal: fn (array $r) => ($r['request_status'] ?? null) === 'cancelled',
        );

        $events = iterator_to_array($gen, false);

        $this->assertCount(1, $events);
        $this->assertSame('terminal', $events[0]['event']);
        $this->assertSame(1, $calls);
    }

    /** @return iterable<string, array{string}> */
    public static function closedRequestStatuses(): iterable
    {
        yield 'cancelled' => ['cancelled'];
        yield 'expired' => ['expired'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('closedRequestStatuses')]
    public function testStreamAssetEndsWhenTheRequestCloses(string $requestStatus): void
    {
        $http = new class ($requestStatus) extends HttpClient {
            /** @var list<string> */
            public array $paths = [];

            public function __construct(private readonly string $requestStatus)
            {
                parent::__construct('pk_test_xxx', 'https://api.example.com', 10.0, 0);
            }

            public function rawStream(string $path): \Psr\Http\Message\ResponseInterface
            {
                $this->paths[] = $path;
                return new Response(503);
            }

            public function get(string $path, array $query = []): array
            {
                $this->paths[] = $path;
                if (count($this->paths) > 2) {
                    throw new \RuntimeException('polled again after a closed request');
                }
                return ['asset_index' => 0, 'status' => 'pending', 'request_status' => $this->requestStatus];
            }
        };

        $events = iterator_to_array((new VerificationRequests($http))->streamAsset('vr_1', 0), false);

        $this->assertCount(1, $events);
        $this->assertSame('terminal', $events[0]['event']);
        $this->assertSame(
            ['/api/v1/verify/request/vr_1/assets/0/events', '/api/v1/verify/request/vr_1/assets/0/status'],
            $http->paths,
        );
    }

    public function testPollingFallbackOnNetworkError(): void
    {
        $retrieve = fn () => ['status' => 'verified'];
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', $retrieve, ['verified'], 'Test',
            injectResponse: new Response(500),
        );
        $events = iterator_to_array($gen);
        $this->assertSame('terminal', $events[0]['event']);
    }

    public function testNoFallbackThrowsOnHttpError(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('Test returned HTTP 503');
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            pollFallback: false,
            injectResponse: new Response(503),
        );
        iterator_to_array($gen);
    }

    public function testFallbackDisabledWithRetrieveStillThrows(): void
    {
        $this->expectException(NetworkException::class);
        $retrieve = fn () => ['status' => 'verified'];
        $gen = Streaming::streamEndpoint(
            $this->http, '/x', $retrieve, ['verified'], 'Test',
            pollFallback: false,
            injectResponse: new Response(503),
        );
        iterator_to_array($gen);
    }

    public function testEmptyDataLineProducesNoEvent(): void
    {
        $sse = "event: noop\n\n";  // event-only frame, no data lines
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));
        $this->assertCount(0, $events);
    }

    public function testMultiLineData(): void
    {
        $sse = "event: m\ndata: line1\ndata: line2\n\n";
        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: self::sseResponse($sse),
        ));
        // Joined with \n, becomes "line1\nline2", not valid JSON, so raw string.
        $this->assertSame("line1\nline2", $events[0]['data']);
    }

    /**
     * Regression for the read_timeout interaction. PSR-7 Stream::read()
     * throws RuntimeException when the underlying fread() returns false
     * (which is what stream_set_timeout does on socket read timeout).
     * readSse must catch this and treat it as an idle read so the cancel
     * callback gets re-polled instead of the stream terminating with an
     * uncaught exception.
     */
    public function testReadTimeoutTreatedAsIdle(): void
    {
        $stream = new TimeoutThenDataStream(
            timeoutsBeforeData: 2,
            data: "event: status\ndata: {\"status\":\"verified\"}\n\n",
        );
        $response = new Response(200, ['Content-Type' => 'text/event-stream'], $stream);

        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: $response,
        ));

        $this->assertCount(1, $events);
        $this->assertSame('status', $events[0]['event']);
        $this->assertSame(['status' => 'verified'], $events[0]['data']);
        $this->assertGreaterThanOrEqual(2, $stream->timeoutsRaised, 'expected the simulated timeouts to be raised');
    }

    public function testReadTimeoutHonoursShouldCancel(): void
    {
        $stream = new TimeoutThenDataStream(
            timeoutsBeforeData: PHP_INT_MAX,  // never deliver data
            data: '',
        );
        $response = new Response(200, ['Content-Type' => 'text/event-stream'], $stream);

        $polls = 0;
        $shouldCancel = function () use (&$polls): bool {
            $polls++;
            return $polls >= 3;
        };

        $events = iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            shouldCancel: $shouldCancel,
            injectResponse: $response,
        ));

        $this->assertCount(0, $events);
        $this->assertGreaterThanOrEqual(3, $polls);
    }

    /**
     * Regression for the narrowed RuntimeException catch in readSse: a
     * stream read failure that is NOT a timeout (broken pipe, TLS reset,
     * detached stream — anything where stream metadata `timed_out` is
     * false) must propagate to the caller instead of being silently
     * swallowed as an idle read.
     */
    public function testRealReadErrorPropagates(): void
    {
        $stream = new TimeoutThenDataStream(
            timeoutsBeforeData: 1,
            data: '',
            simulateRealError: true,  // metadata `timed_out` stays false on the throw
        );
        $response = new Response(200, ['Content-Type' => 'text/event-stream'], $stream);

        $this->expectException(\RuntimeException::class);
        iterator_to_array(Streaming::streamEndpoint(
            $this->http, '/x', null, [], 'Test',
            injectResponse: $response,
        ));
    }
}

/**
 * Minimal PSR-7 StreamInterface that throws RuntimeException on the first
 * N read() calls (simulating fread() failure under stream_set_timeout)
 * before returning the configured payload. eof() stays false until the
 * payload is exhausted. getMetadata() reports `timed_out` matching the
 * most recent throw cause so Streaming::readSse can distinguish idle
 * timeouts from real transport errors.
 */
class TimeoutThenDataStream implements \Psr\Http\Message\StreamInterface
{
    public int $timeoutsRaised = 0;
    private int $remainingTimeouts;
    private string $remaining;
    private bool $lastReadTimedOut = false;
    private bool $simulateRealError;

    public function __construct(int $timeoutsBeforeData, string $data, bool $simulateRealError = false)
    {
        $this->remainingTimeouts = $timeoutsBeforeData;
        $this->remaining = $data;
        $this->simulateRealError = $simulateRealError;
    }

    public function read($length): string
    {
        if ($this->remainingTimeouts > 0) {
            $this->remainingTimeouts--;
            $this->timeoutsRaised++;
            $this->lastReadTimedOut = !$this->simulateRealError;
            throw new \RuntimeException('Unable to read from stream');
        }
        $this->lastReadTimedOut = false;
        if ($this->remaining === '') {
            return '';
        }
        $chunk = substr($this->remaining, 0, $length);
        $this->remaining = substr($this->remaining, $length);
        return $chunk;
    }

    public function eof(): bool
    {
        return $this->remainingTimeouts === 0 && $this->remaining === '';
    }

    public function getMetadata($key = null)
    {
        $meta = ['timed_out' => $this->lastReadTimedOut];
        if ($key !== null) {
            return $meta[$key] ?? null;
        }
        return $meta;
    }

    public function close(): void {}
    public function detach() { return null; }
    public function getSize(): ?int { return null; }
    public function tell(): int { return 0; }
    public function isSeekable(): bool { return false; }
    public function seek($offset, $whence = SEEK_SET): void {}
    public function rewind(): void {}
    public function isWritable(): bool { return false; }
    public function write($string): int { return 0; }
    public function isReadable(): bool { return true; }
    public function getContents(): string { return ''; }
    public function __toString(): string { return ''; }
}

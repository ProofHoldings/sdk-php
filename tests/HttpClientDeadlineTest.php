<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Exceptions\TimeoutException;
use ProofHoldings\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The wall-clock deadline inside `HttpClient::fetchPublic` (round-3 review).
 *
 * Guzzle's `timeout` bounds ONE read, and a loop that reads until the ceiling restarts it on
 * every chunk — so a server that keeps trickling can hold a call far past its own timeout. That
 * is hard to stage over a real socket (the per-read timeout usually fires first, measured at
 * 1.01s for a body arriving 50ms at a time), so the deadline is pinned here against a stream
 * that always answers, just slowly: no transport timeout can fire, and only the deadline can
 * stop the loop.
 */
class HttpClientDeadlineTest extends TestCase
{
    public function testStopsReadingOnceTheDeadlinePasses(): void
    {
        $stream = new SlowStream(pauseMicros: 120_000);
        $http = new HttpClient('pk_test_123', 'https://api.example.test', 5.0, 0);

        $client = new Client([
            'handler' => static fn (RequestInterface $r, array $o) => Create::promiseFor(
                new Response(200, ['content-type' => 'application/statuslist+jwt'], $stream)
            ),
            'http_errors' => false,
        ]);
        (new \ReflectionProperty(HttpClient::class, 'publicClient'))->setValue($http, $client);

        $startedAt = microtime(true);
        try {
            $http->fetchPublic('https://api.example.test/api/v1/proofs/status-list', 0.5);
            $this->fail('expected the read to give up on its deadline');
        } catch (TimeoutException $e) {
            $elapsed = microtime(true) - $startedAt;
            // The stream never ends and never errors: without the deadline this runs until the
            // 512 KiB ceiling, which at this pace is over seventeen hours.
            $this->assertLessThan(3.0, $elapsed);
            $this->assertGreaterThanOrEqual(0.5, $elapsed);
        }
    }
}

/** A readable stream that always answers — one byte at a time, after a pause. */
final class SlowStream implements StreamInterface
{
    private int $served = 0;

    /**
     * $limit is FINITE on purpose: with an endless stream, removing the deadline would hang the
     * suite instead of failing it, and a guard that can only hang is not a guard. Twenty bytes at
     * this pace take 2.4s, comfortably past the 0.5s deadline and comfortably inside a test.
     */
    public function __construct(private readonly int $pauseMicros, private readonly int $limit = 20)
    {
    }

    public function read(int $length): string
    {
        if ($this->served >= $this->limit) {
            return '';
        }
        usleep($this->pauseMicros);
        $this->served++;

        return 'x';
    }

    public function eof(): bool
    {
        return $this->served >= $this->limit;
    }

    public function __toString(): string
    {
        return '';
    }

    public function close(): void
    {
    }

    public function detach()
    {
        return null;
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function tell(): int
    {
        return 0;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
    }

    public function rewind(): void
    {
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        return 0;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function getContents(): string
    {
        return '';
    }

    public function getMetadata(?string $key = null)
    {
        return $key === null ? [] : null;
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use ProofHoldings\Exceptions\NetworkException;
use ProofHoldings\Exceptions\PollingTimeoutException;

/**
 * SSE-with-polling-fallback streaming for PHP, contract-compliant with the
 * other SDKs (JS/Python/Go).
 *
 * PHP has no native AbortSignal; cancellation is via a callable that returns
 * true to abort. The generator yields associative arrays shaped:
 *
 *   ['event' => string, 'id' => ?string, 'data' => mixed]
 *
 * For SSE: ``event`` is the SSE event name (default ``message``), ``data`` is
 * the JSON-decoded payload (or the raw string if not JSON).
 *
 * For polling fallback: ``event`` is ``poll`` for non-terminal observations
 * and ``terminal`` for the final terminal observation. ``data`` is the full
 * resource array as returned by ``$retrieve``.
 */
class Streaming
{
    /**
     * Open an SSE stream and yield parsed events. On any SSE failure (503,
     * non-2xx, transport error, or no fallback configured), fall back to
     * polling ``$retrieve`` under the standard contract.
     *
     * @param HttpClient                  $http
     * @param string                      $path           Absolute API path (e.g. ``/api/v1/verifications/ver_1/events``)
     * @param (callable(): array)|null    $retrieve       Polling fallback retrieve. ``null`` disables fallback.
     * @param list<string>                $terminalStates Terminal status values for polling fallback.
     * @param string                      $label          Human-readable label for error messages.
     * @param (callable(): bool)|null     $shouldCancel   Returning true aborts the generator.
     * @param bool                        $pollFallback   When false, surface SSE errors as exceptions.
     * @param ResponseInterface|null      $injectResponse Test seam: skip openSseRequest and use this response instead.
     * @param (callable(array): bool)|null $isTerminal   Additional terminal test for a polled resource whose own
     *                                                  status cannot say it is done (an asset that stays pending
     *                                                  after its request was cancelled).
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public static function streamEndpoint(
        HttpClient $http,
        string $path,
        ?callable $retrieve,
        array $terminalStates,
        string $label,
        ?callable $shouldCancel = null,
        bool $pollFallback = true,
        ?ResponseInterface $injectResponse = null,
        ?callable $isTerminal = null,
    ): \Generator {
        $response = $injectResponse;
        if ($response === null) {
            try {
                $response = $http->rawStream($path);
            } catch (GuzzleException | NetworkException $e) {
                if ($pollFallback && $retrieve !== null) {
                    yield from self::pollingFallback($retrieve, $terminalStates, $label, $shouldCancel, $isTerminal);
                    return;
                }
                throw new NetworkException("SSE stream for {$label} failed: " . $e->getMessage());
            }
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            if ($pollFallback && $retrieve !== null) {
                yield from self::pollingFallback($retrieve, $terminalStates, $label, $shouldCancel, $isTerminal);
                return;
            }
            throw new NetworkException("SSE stream for {$label} returned HTTP {$statusCode}");
        }

        yield from self::readSse($response, $shouldCancel);
    }

    /**
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    private static function readSse(ResponseInterface $response, ?callable $shouldCancel): \Generator
    {
        $body = $response->getBody();
        $buffer = '';
        try {
            while (!$body->eof()) {
                if ($shouldCancel !== null && $shouldCancel()) {
                    return;
                }
                try {
                    $chunk = $body->read(8192);
                } catch (\RuntimeException $e) {
                    // PSR-7 Stream::read throws on fread() === false. The
                    // expected case is a socket read timeout (HttpClient::rawStream
                    // sets read_timeout) — only that should fall through to the
                    // idle branch. Real transport errors (broken pipe, TLS reset,
                    // detached stream) must propagate so callers see the failure
                    // instead of an indefinite silent loop.
                    if (!self::isReadTimeout($body)) {
                        throw $e;
                    }
                    $chunk = '';
                }
                if ($chunk === '') {
                    usleep(50_000);
                    continue;
                }
                $buffer .= $chunk;

                while (($sep = self::findFrameBoundary($buffer)) !== false) {
                    $rawFrame = substr($buffer, 0, $sep);
                    $skipLen = str_starts_with(substr($buffer, $sep), "\r\n\r\n") ? 4 : 2;
                    $buffer = substr($buffer, $sep + $skipLen);
                    $parsed = self::parseFrame($rawFrame);
                    if ($parsed !== null) {
                        yield $parsed;
                    }
                }
            }
            // Drain final frame if buffer non-empty and no trailing blank line.
            if ($buffer !== '') {
                $parsed = self::parseFrame($buffer);
                if ($parsed !== null) {
                    yield $parsed;
                }
            }
        } finally {
            $body->close();
        }
    }

    /**
     * Without this check, broken pipes / TLS resets / detached streams
     * would be silently swallowed as idle reads, leaving callers blind
     * to real transport failures. The PSR-7 metadata array proxies to
     * stream_get_meta_data() for resource-backed bodies (Guzzle's
     * StreamHandler always uses one), and the `timed_out` key is set by
     * the PHP runtime only when the most recent fread() hit the socket
     * timeout — so it cleanly distinguishes the two cases.
     */
    private static function isReadTimeout(\Psr\Http\Message\StreamInterface $body): bool
    {
        $meta = $body->getMetadata();
        if (!is_array($meta)) {
            return false;
        }
        return ($meta['timed_out'] ?? false) === true;
    }

    private static function findFrameBoundary(string $buffer): false|int
    {
        $a = strpos($buffer, "\n\n");
        $b = strpos($buffer, "\r\n\r\n");
        if ($a === false) return $b;
        if ($b === false) return $a;
        return min($a, $b);
    }

    /**
     * @return array{event: string, id: ?string, data: mixed}|null
     */
    private static function parseFrame(string $frame): ?array
    {
        $event = 'message';
        $id = null;
        $dataLines = [];

        foreach (preg_split('/\r?\n/', $frame) ?: [] as $rawLine) {
            if ($rawLine === '' || str_starts_with($rawLine, ':')) continue;
            $colon = strpos($rawLine, ':');
            if ($colon === false) {
                $field = $rawLine;
                $value = '';
            } else {
                $field = substr($rawLine, 0, $colon);
                $value = substr($rawLine, $colon + 1);
                if (str_starts_with($value, ' ')) {
                    $value = substr($value, 1);
                }
            }

            switch ($field) {
                case 'event': $event = $value; break;
                case 'id':    $id = $value; break;
                case 'data':  $dataLines[] = $value; break;
            }
        }

        if (empty($dataLines)) return null;
        $raw = implode("\n", $dataLines);
        $decoded = json_decode($raw, true);
        $data = $decoded !== null || $raw === 'null' ? $decoded : $raw;
        return ['event' => $event, 'id' => $id, 'data' => $data];
    }

    /**
     * Polling fallback. Yields each poll observation as an event with
     * ``event => 'poll'`` until a terminal status is reached, then yields a
     * final ``event => 'terminal'`` event.
     *
     * @param callable(): array       $retrieve
     * @param list<string>            $terminalStates
     * @param string                  $label
     * @param (callable(): bool)|null $shouldCancel
     * @param (callable(array): bool)|null $isTerminal
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    private static function pollingFallback(
        callable $retrieve,
        array $terminalStates,
        string $label,
        ?callable $shouldCancel,
        ?callable $isTerminal = null,
    ): \Generator {
        $interval = Polling::DEFAULT_INTERVAL;
        $maxInterval = Polling::DEFAULT_MAX_INTERVAL;
        $backoff = Polling::DEFAULT_BACKOFF;
        $jitter = Polling::DEFAULT_JITTER;
        $timeout = Polling::DEFAULT_TIMEOUT;
        $start = hrtime(true);

        while (true) {
            if ($shouldCancel !== null && $shouldCancel()) {
                return;
            }
            $resource = $retrieve();
            // Re-check cancel after the synchronous retrieve so a deadline
            // that tripped mid-request isn't masked by yielding one more
            // event before honouring the cancel.
            if ($shouldCancel !== null && $shouldCancel()) {
                return;
            }
            $status = $resource['status'] ?? null;
            $terminal = ($status !== null && in_array($status, $terminalStates, true))
                || ($isTerminal !== null && $isTerminal($resource));
            yield [
                'event' => $terminal ? 'terminal' : 'poll',
                'id' => null,
                'data' => $resource,
            ];
            if ($terminal) return;

            $elapsed = (hrtime(true) - $start) / 1e9;
            if ($elapsed >= $timeout) {
                throw new PollingTimeoutException(
                    "{$label} streaming did not complete within {$timeout}s (last status: " . ($status ?? 'unknown') . ')'
                );
            }

            $jitterAmount = $jitter > 0 ? mt_rand() / mt_getrandmax() * $jitter : 0.0;
            $sleepSeconds = $interval + $jitterAmount;
            usleep((int) ($sleepSeconds * 1_000_000));
            $interval = min($interval * $backoff, $maxInterval);
        }
    }
}

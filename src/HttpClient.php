<?php

declare(strict_types=1);

namespace ProofHoldings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use ProofHoldings\Exceptions\NetworkException;
use ProofHoldings\Exceptions\ProofException;
use ProofHoldings\Exceptions\TimeoutException;

class HttpClient
{
    private Client $client;

    /**
     * A second, credential-free client for PUBLIC artifacts — see `fetchPublic`. Built lazily so
     * a caller that never verifies a proof offline never pays for it.
     */
    private ?Client $publicClient = null;

    public function __construct(
        private readonly string $apiKey,
        public readonly string $baseUrl,
        private readonly float $timeout,
        private readonly int $maxRetries,
    ) {
        $this->client = new Client([
            'base_uri' => $this->normalizedBaseUri(),
            'timeout' => $timeout,
            'headers' => $this->defaultHeaders() + [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Shared headers applied to every outbound request (primary client and
     * the ephemeral streaming client). Keeps auth and UA wiring in one
     * place so `rawStream()` cannot drift away from the main client's
     * identity on the wire.
     *
     * @return array<string, string>
     */
    private function defaultHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
            'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
        ];
    }

    private function normalizedBaseUri(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    /**
     * Fetch a PUBLIC artifact by absolute url — no API key, no redirects, body capped.
     *
     * The key is left off deliberately rather than forgotten: the issuer's status list is a
     * shared document whose whole point is that reading it says nothing about WHICH proof is
     * being checked. Sending a credential with it would hand the issuer the one fact the design
     * withholds. Redirects are refused because the caller has already decided the url's origin is
     * trusted, and a redirect would move it.
     *
     * @param array<string, string> $headers
     * @return array{status: int, body: string, declared_length: int|null}
     */
    public function fetchPublic(
        string $url,
        float $timeout,
        array $headers = [],
        int $maxBytes = 524288,
    ): array {
        if ($this->publicClient === null) {
            $config = [
                'allow_redirects' => false,
                'http_errors' => false,
                'headers' => ['User-Agent' => 'proof-sdk-php/' . Version::VERSION],
            ];
            // `stream` is read by `Proxy::wrapStreaming`, which `Utils::chooseHandler` only puts
            // in place when `allow_url_fopen` is on. Elsewhere curl handles the request and
            // downloads the whole body into a sink regardless — so the handler is forced here
            // when it can be, exactly as `rawStream()` does it, and the ceiling below is a real
            // memory bound rather than a claim about one.
            if (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOL)) {
                $config['handler'] = HandlerStack::create(new StreamHandler());
            }
            $this->publicClient = new Client($config);
        }

        $response = $this->publicClient->get($url, [
            'headers' => $headers,
            'timeout' => $timeout,
            'stream' => true,
        ]);

        $declared = $response->getHeaderLine('Content-Length');

        return [
            'status' => $response->getStatusCode(),
            'body' => $this->readCapped($response->getBody(), $maxBytes + 1, $timeout),
            'declared_length' => $declared === '' ? null : (int) $declared,
        ];
    }

    /**
     * Read at most $limit bytes off a stream, under a WALL-CLOCK deadline.
     *
     * Two things a plain `read($limit)` does not do. It returns whatever one `fread` got — on a
     * socket that is one packet, measured at 8192 bytes of a 100000-byte document — so a single
     * read silently truncates. And Guzzle's `timeout` bounds one read, not the transfer: a server
     * dripping one byte a second held an 8-second call under a 2-second timeout, because every
     * byte restarted the clock. The loop below fixes the first and the deadline the second.
     */
    private function readCapped(StreamInterface $stream, int $limit, float $timeout): string
    {
        $deadline = microtime(true) + $timeout;
        $body = '';

        while (strlen($body) < $limit && !$stream->eof()) {
            if (microtime(true) >= $deadline) {
                throw new TimeoutException(
                    sprintf('Reading the response body exceeded %.1fs', $timeout)
                );
            }
            $chunk = $stream->read($limit - strlen($body));
            if ($chunk === '') {
                break;
            }
            $body .= $chunk;
        }

        return $body;
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query);
    }

    public function post(string $path, ?array $body = null): array
    {
        return $this->request('POST', $path, body: $body);
    }

    public function put(string $path, ?array $body = null): array
    {
        return $this->request('PUT', $path, body: $body);
    }

    public function patch(string $path, ?array $body = null): array
    {
        return $this->request('PATCH', $path, body: $body);
    }

    public function delete(string $path, ?array $body = null): array
    {
        return $this->request('DELETE', $path, body: $body);
    }

    /**
     * Open a streaming GET to $path. Used by Streaming for SSE — bypasses
     * the JSON-decode-and-retry path because SSE streams are long-lived and
     * delivered chunked. Caller is responsible for reading and closing the
     * body. Failures (network, non-2xx) surface via the returned response's
     * status code, not as exceptions.
     *
     * `read_timeout` bounds each socket read, not the whole stream. A short
     * read timeout is what makes a consumer-level cancel callable (see
     * Streaming::readSse) actually effective: without it, a silent server
     * can keep read() blocked until PHP's `default_socket_timeout` (often
     * 60s or unset) before the cancel is re-checked.
     *
     * `read_timeout` is a StreamHandler-only option (CurlHandler buffers
     * the full response via CURLOPT_WRITEFUNCTION and ignores it). When
     * `allow_url_fopen=1` — i.e. the StreamHandler is actually viable —
     * we force it so streaming is cancellable; when the HTTP wrapper is
     * disabled we keep Guzzle's default (curl) so rawStream still works,
     * accepting curl's coarser timeout behavior on those hardened hosts.
     *
     * Known limitation: `read_timeout` only begins once headers have been
     * received. A server that accepts TCP/TLS but stalls before sending
     * headers will still block until PHP's `default_socket_timeout` (~60s)
     * — StreamHandler exposes no way to bound just the handshake phase
     * without also bounding the long-lived body stream. Acceptable for
     * our CI use case; a hostile server on this path would need a
     * dedicated pre-flight check.
     */
    public function rawStream(string $path): ResponseInterface
    {
        $clientOptions = [
            'base_uri' => $this->normalizedBaseUri(),
            'timeout' => 0,
            'stream' => true,
            'read_timeout' => 2.0,
            'headers' => $this->defaultHeaders() + [
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
            ],
        ];
        if (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            $clientOptions['handler'] = HandlerStack::create(new StreamHandler());
        }
        $client = new Client($clientOptions);
        return $client->request('GET', $path, [
            'stream' => true,
            'http_errors' => false,
        ]);
    }

    private function request(string $method, string $path, ?array $body = null, array $query = []): array
    {
        // Filter null values from query
        $query = array_filter($query, fn($v) => $v !== null);

        $lastException = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $options = [];
                if (!empty($query)) {
                    $options['query'] = $query;
                }
                if ($body !== null) {
                    $options['json'] = $body;
                }

                $response = $this->client->request($method, $path, $options);
                $statusCode = $response->getStatusCode();
                $contents = $response->getBody()->getContents();

                // Non-JSON success (e.g. avatar images): return a
                // {data, content_type} envelope instead of attempting a JSON
                // decode. Only when the server EXPLICITLY declares a non-JSON
                // content type — responses without a Content-Type header keep
                // the legacy JSON-decode path.
                $contentType = $response->getHeaderLine('Content-Type');
                if ($contents !== '' && $contentType !== '' && !str_contains($contentType, 'application/json')) {
                    return ['data' => $contents, 'content_type' => $contentType];
                }

                $responseBody = json_decode($contents, true) ?: [];

                return $responseBody;
            } catch (RequestException $e) {
                $response = $e->getResponse();

                if ($response === null) {
                    // Network error with no response
                    $lastException = $e;
                    if ($attempt < $this->maxRetries) {
                        usleep($this->backoffMicroseconds($attempt));
                        continue;
                    }
                    throw new NetworkException($e->getMessage());
                }

                $statusCode = $response->getStatusCode();
                $responseBody = json_decode($response->getBody()->getContents(), true) ?: [];

                // Rate limiting — retry with backoff
                if ($statusCode === 429 && $attempt < $this->maxRetries) {
                    $retryAfter = $response->getHeaderLine('Retry-After');
                    $delay = $retryAfter !== ''
                        ? (int) ((float) $retryAfter * 1_000_000)
                        : $this->backoffMicroseconds($attempt);
                    usleep($delay);
                    continue;
                }

                // Server errors — retry with backoff
                if ($statusCode >= 500 && $attempt < $this->maxRetries) {
                    usleep($this->backoffMicroseconds($attempt));
                    continue;
                }

                // Throw typed exception
                $apiError = $responseBody['error'] ?? null;
                throw ProofException::fromResponse($statusCode, $apiError);
            } catch (ConnectException $e) {
                $lastException = $e;
                if (str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'timeout')) {
                    if ($attempt < $this->maxRetries) {
                        usleep($this->backoffMicroseconds($attempt));
                        continue;
                    }
                    throw new TimeoutException("Request to {$method} {$path} timed out after {$this->timeout}s");
                }
                if ($attempt < $this->maxRetries) {
                    usleep($this->backoffMicroseconds($attempt));
                    continue;
                }
            }
        }

        throw new NetworkException($lastException?->getMessage() ?? 'Network request failed');
    }

    private function backoffMicroseconds(int $attempt): int
    {
        return min((int) (1_000_000 * pow(2, $attempt)), 10_000_000);
    }
}

<?php

declare(strict_types=1);

namespace Proof;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Proof\Exceptions\NetworkException;
use Proof\Exceptions\ProofException;
use Proof\Exceptions\TimeoutException;

class HttpClient
{
    private Client $client;

    public function __construct(
        private readonly string $apiKey,
        public readonly string $baseUrl,
        private readonly float $timeout,
        private readonly int $maxRetries,
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($baseUrl, '/'),
            'timeout' => $timeout,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
            ],
        ]);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, query: $query);
    }

    public function post(string $path, ?array $body = null): array
    {
        return $this->request('POST', $path, body: $body);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
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
                $responseBody = json_decode($response->getBody()->getContents(), true) ?: [];

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

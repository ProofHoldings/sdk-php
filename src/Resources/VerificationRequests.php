<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;
use ProofHoldings\Streaming;

class VerificationRequests
{
    private const array TERMINAL_STATES = Polling::REQUEST_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): array
    {
        return $this->http->post('/api/v1/verification-requests', $params);
    }

    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/verification-requests/' . rawurlencode($id));
    }

    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/verification-requests', $params);
    }

    /** Get a verification request by its reference ID. */
    public function getByReference(string $referenceId): array
    {
        return $this->http->get('/api/v1/verification-requests/by-reference/' . rawurlencode($referenceId));
    }

    public function cancel(string $id): array
    {
        return $this->http->delete('/api/v1/verification-requests/' . rawurlencode($id));
    }

    /** Get proof tokens for verified assets of a verification request. */
    public function getProofs(string $id): array
    {
        return $this->http->get('/api/v1/verification-requests/' . rawurlencode($id) . '/proofs');
    }

    /**
     * Stream a single asset's verification status via SSE with polling fallback.
     * Endpoint: GET /api/v1/verify/request/:id/assets/:assetIndex/events
     *
     * The server closes the stream after the asset verifies or its request is cancelled or
     * expires; the polling fallback ends on the same outcomes.
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function streamAsset(string $id, int $assetIndex, ?callable $shouldCancel = null): \Generator
    {
        $rid = rawurlencode($id);
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/verify/request/' . $rid . '/assets/' . $assetIndex . '/events',
            fn () => $this->http->get('/api/v1/verify/request/' . $rid . '/assets/' . $assetIndex . '/status'),
            ['verified', 'failed', 'expired', 'revoked'],
            "Verification request {$id} asset {$assetIndex}",
            $shouldCancel,
            isTerminal: fn (array $asset) => in_array($asset['request_status'] ?? null, ['cancelled', 'expired'], true),
        );
    }

    public function waitForCompletion(
        string $id,
        float $interval = Polling::DEFAULT_INTERVAL,
        float $timeout = Polling::DEFAULT_TIMEOUT,
        float $backoff = Polling::DEFAULT_BACKOFF,
        float $maxInterval = Polling::DEFAULT_MAX_INTERVAL,
        float $jitter = Polling::DEFAULT_JITTER,
        ?callable $shouldCancel = null,
    ): array {
        return Polling::waitUntilComplete(
            fn () => $this->retrieve($id),
            self::TERMINAL_STATES,
            "Verification request {$id}",
            $interval,
            $timeout,
            $backoff,
            $maxInterval,
            $jitter,
            $shouldCancel,
        );
    }
}

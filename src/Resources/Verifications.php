<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;
use ProofHoldings\Streaming;

class Verifications
{
    private const array TERMINAL_STATES = Polling::VERIFICATION_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): array
    {
        return $this->http->post('/api/v1/verifications', $params);
    }

    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/verifications/' . rawurlencode($id));
    }

    /** Create a multi-channel verification group (one phone offered across several channels; first completed channel wins). */
    public function createMultiChannel(array $params): array
    {
        return $this->http->post('/api/v1/verifications/multi-channel', $params);
    }

    /** Get multi-channel group status (poll until verified/expired; proof is present when verified). */
    public function getMultiChannelStatus(string $groupId): array
    {
        return $this->http->get('/api/v1/verifications/multi-channel/' . rawurlencode($groupId) . '/status');
    }

    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/verifications', $params);
    }

    public function verify(string $id): array
    {
        return $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/verify');
    }

    public function submit(string $id, string $code): array
    {
        return $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/submit', ['code' => $code]);
    }

    /** Resend a verification email (email channel only). */
    public function resend(string $id): array
    {
        return $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/resend');
    }

    /** Auto-complete a verification in test mode (pk_test_* API keys only). */
    public function testVerify(string $id): array
    {
        return $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/test-verify');
    }

    /** List verified users grouped by external_user_id. */
    public function listVerifiedUsers(array $params = []): array
    {
        return $this->http->get('/api/v1/verifications/users', $params);
    }

    /** Get a single verified user's verifications by external user ID. */
    public function getVerifiedUser(string $externalUserId): array
    {
        return $this->http->get('/api/v1/verifications/users/' . rawurlencode($externalUserId));
    }

    /** Start a B2B domain verification. */
    public function startDomainVerification(array $params): array
    {
        return $this->http->post('/api/v1/verifications/domain', $params);
    }

    /** Check a pending domain verification (DNS/HTTP file). */
    public function checkDomainVerification(string $id): array
    {
        return $this->http->post('/api/v1/verifications/domain/' . rawurlencode($id) . '/check');
    }

    /**
     * Delete a domain verification attempt that is not verified (pending, verifying or failed) —
     * nothing is revoked. A verified domain, or one that needs re-verification, is refused with 409
     * `domain_verified`: removing it revokes its proofs and every delegation issued from it, which
     * belongs to the signed-in owner (the dashboard, or the MCP `delete_domain` tool in a login session).
     *
     * @return array<string, mixed>
     */
    public function deleteDomainVerification(string $id): array
    {
        return $this->http->delete('/api/v1/verifications/domain/' . rawurlencode($id));
    }

    /**
     * Poll until verification reaches a terminal state.
     *
     * Uses exponential backoff with jitter per the standard polling contract.
     * Pass a ``$shouldCancel`` callback returning true to abort early.
     */
    /**
     * Stream verification status via SSE with polling fallback.
     * Endpoint: GET /api/v1/verifications/:id/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function stream(string $id, ?callable $shouldCancel = null): \Generator
    {
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/verifications/' . rawurlencode($id) . '/events',
            fn () => $this->retrieve($id),
            self::TERMINAL_STATES,
            "Verification {$id}",
            $shouldCancel,
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
            "Verification {$id}",
            $interval,
            $timeout,
            $backoff,
            $maxInterval,
            $jitter,
            $shouldCancel,
        );
    }
}

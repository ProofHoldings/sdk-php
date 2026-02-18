<?php

declare(strict_types=1);

namespace Proof\Resources;

use Proof\HttpClient;
use Proof\Polling;

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
     * Poll until verification reaches a terminal state.
     *
     * @param float $interval Poll interval in seconds (default: 3.0)
     * @param float $timeout  Timeout in seconds (default: 600.0)
     */
    public function waitForCompletion(string $id, float $interval = Polling::DEFAULT_INTERVAL, float $timeout = Polling::DEFAULT_TIMEOUT): array
    {
        return Polling::waitUntilComplete(
            fn () => $this->retrieve($id),
            self::TERMINAL_STATES,
            "Verification {$id}",
            $interval,
            $timeout,
        );
    }
}

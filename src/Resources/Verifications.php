<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;
use ProofHoldings\Types\DomainCheckResponse;
use ProofHoldings\Types\DomainVerificationResponse;
use ProofHoldings\Types\ResendVerificationResponse;
use ProofHoldings\Types\TestVerifyResponse;
use ProofHoldings\Types\Verification;
use ProofHoldings\Types\VerificationListResponse;
use ProofHoldings\Types\VerifiedUserDetail;
use ProofHoldings\Types\VerifiedUserListResponse;

class Verifications
{
    private const array TERMINAL_STATES = Polling::VERIFICATION_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): Verification
    {
        $data = $this->http->post('/api/v1/verifications', $params);
        return Verification::fromArray($data);
    }

    public function retrieve(string $id): Verification
    {
        $data = $this->http->get('/api/v1/verifications/' . rawurlencode($id));
        return Verification::fromArray($data);
    }

    public function list(array $params = []): VerificationListResponse
    {
        $data = $this->http->get('/api/v1/verifications', $params);
        return VerificationListResponse::fromArray($data);
    }

    public function verify(string $id): Verification
    {
        $data = $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/verify');
        return Verification::fromArray($data);
    }

    public function submit(string $id, string $code): Verification
    {
        $data = $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/submit', ['code' => $code]);
        return Verification::fromArray($data);
    }

    /** Resend a verification email (email channel only). */
    public function resend(string $id): ResendVerificationResponse
    {
        $data = $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/resend');
        return ResendVerificationResponse::fromArray($data);
    }

    /** Auto-complete a verification in test mode (pk_test_* API keys only). */
    public function testVerify(string $id): TestVerifyResponse
    {
        $data = $this->http->post('/api/v1/verifications/' . rawurlencode($id) . '/test-verify');
        return TestVerifyResponse::fromArray($data);
    }

    /** List verified users grouped by external_user_id. */
    public function listVerifiedUsers(array $params = []): VerifiedUserListResponse
    {
        $data = $this->http->get('/api/v1/verifications/users', $params);
        return VerifiedUserListResponse::fromArray($data);
    }

    /** Get a single verified user's verifications by external user ID. */
    public function getVerifiedUser(string $externalUserId): VerifiedUserDetail
    {
        $data = $this->http->get('/api/v1/verifications/users/' . rawurlencode($externalUserId));
        return VerifiedUserDetail::fromArray($data);
    }

    /** Start a B2B domain verification. */
    public function startDomainVerification(array $params): DomainVerificationResponse
    {
        $data = $this->http->post('/api/v1/verifications/domain', $params);
        return DomainVerificationResponse::fromArray($data);
    }

    /** Check a pending domain verification (DNS/HTTP file). */
    public function checkDomainVerification(string $id): DomainCheckResponse
    {
        $data = $this->http->post('/api/v1/verifications/domain/' . rawurlencode($id) . '/check');
        return DomainCheckResponse::fromArray($data);
    }

    /**
     * Poll until verification reaches a terminal state.
     *
     * @param float $interval Poll interval in seconds (default: 3.0)
     * @param float $timeout  Timeout in seconds (default: 600.0)
     */
    public function waitForCompletion(string $id, float $interval = Polling::DEFAULT_INTERVAL, float $timeout = Polling::DEFAULT_TIMEOUT): Verification
    {
        $data = Polling::waitUntilComplete(
            fn () => $this->http->get('/api/v1/verifications/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Verification {$id}",
            $interval,
            $timeout,
        );
        return Verification::fromArray($data);
    }
}

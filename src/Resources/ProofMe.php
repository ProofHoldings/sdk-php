<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\IdentityChallengeResponse;

/**
 * Proof-Me — anti-impersonation reverse verification via cross-channel identity
 * challenges (CONFIRM). Enrollment of trusted contacts lives on the Circle
 * people-primitive — see the Circles resource for member management and drills.
 */
class ProofMe
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a cross-channel identity challenge (CONFIRM).
     *
     * @param array<string, mixed> $params Must include `circle_id` and `member_id`; optionally
     *                                     `suspicious_channel`.
     */
    public function createIdentityChallenge(array $params): IdentityChallengeResponse
    {
        $data = $this->http->post('/api/v1/identity-challenges', $params);
        return IdentityChallengeResponse::fromArray($data);
    }

    /** Get an identity challenge by ID (poll for resolution). */
    public function getIdentityChallenge(string $id): IdentityChallengeResponse
    {
        $data = $this->http->get('/api/v1/identity-challenges/' . rawurlencode($id));
        return IdentityChallengeResponse::fromArray($data);
    }
}

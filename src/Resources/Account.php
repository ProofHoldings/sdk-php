<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;

class Account
{
    public function __construct(private readonly HttpClient $http) {}

    /** Initiate account deletion flow. */
    public function initiateDeletion(): array
    {
        return $this->http->post('/api/v1/me/account/delete', []);
    }

    /** Get account deletion session status. */
    public function deletionStatus(string $sessionId): array
    {
        return $this->http->get('/api/v1/me/account/delete/' . rawurlencode($sessionId));
    }

    /** Verify account deletion via email code. */
    public function verifyDeletion(string $sessionId, array $params): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/account/delete/' . rawurlencode($sessionId) . '/verify', $params);
        return SuccessResponse::fromArray($data);
    }

    /** Verify account deletion via magic link. */
    public function verifyDeletionMagicLink(string $token): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/account/delete/magic/' . rawurlencode($token));
        return SuccessResponse::fromArray($data);
    }

    /** Finalize account deletion (requires confirmed session_id). */
    public function delete(array $params): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/account', $params);
        return SuccessResponse::fromArray($data);
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\AuthUser;
use ProofHoldings\Types\ListSessionsResponse;
use ProofHoldings\Types\SuccessResponse;

class Auth
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get the current authenticated user. */
    public function getMe(): AuthUser
    {
        $data = $this->http->get('/api/v1/auth/me');
        return AuthUser::fromArray($data);
    }

    /** List all active sessions for the current user. */
    public function listSessions(): ListSessionsResponse
    {
        $data = $this->http->get('/api/v1/auth/sessions');
        return ListSessionsResponse::fromArray($data);
    }

    /** Revoke a specific session. */
    public function revokeSession(string $sessionId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/auth/sessions/' . rawurlencode($sessionId));
        return SuccessResponse::fromArray($data);
    }
}

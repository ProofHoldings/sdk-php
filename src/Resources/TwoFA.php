<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;

class TwoFA
{
    public function __construct(private readonly HttpClient $http) {}

    /** Start a 2FA session. Params must include action_type and channel. */
    public function start(array $params): array
    {
        return $this->http->post('/api/v1/me/2fa/start', $params);
    }

    /** Poll 2FA session status. */
    public function getStatus(string $sessionId): array
    {
        return $this->http->get('/api/v1/me/2fa/' . rawurlencode($sessionId));
    }

    /** Verify 2FA code. */
    public function verify(string $sessionId, array $params): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/2fa/' . rawurlencode($sessionId) . '/verify', $params);
        return SuccessResponse::fromArray($data);
    }

    /** Verify 2FA via magic link. */
    public function verifyMagicLink(string $token): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/2fa/magic/' . rawurlencode($token));
        return SuccessResponse::fromArray($data);
    }
}

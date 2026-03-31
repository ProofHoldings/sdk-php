<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

class UserDomainVerify
{
    public function __construct(private readonly HttpClient $http) {}

    /** Start domain verification (self-service). */
    public function start(array $params): array
    {
        return $this->http->post('/api/v1/me/verify/domain', $params);
    }

    /** Poll domain verification status. */
    public function status(string $sessionId): array
    {
        return $this->http->get('/api/v1/me/verify/domain/' . rawurlencode($sessionId));
    }

    /** Check domain verification. */
    public function check(string $sessionId): array
    {
        return $this->http->post('/api/v1/me/verify/domain/' . rawurlencode($sessionId) . '/check');
    }
}

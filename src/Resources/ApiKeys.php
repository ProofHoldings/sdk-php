<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\APIKeyResponse;
use ProofHoldings\Types\SuccessResponse;

class ApiKeys
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all API keys. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/api-keys');
    }

    /** Create a new API key. */
    public function create(array $params = []): APIKeyResponse
    {
        $data = $this->http->post('/api/v1/me/api-keys', $params);
        return APIKeyResponse::fromArray($data);
    }

    /** Revoke an API key. */
    public function revoke(string $keyId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/api-keys/' . rawurlencode($keyId));
        return SuccessResponse::fromArray($data);
    }

    /** Regenerate an API key. */
    public function regenerate(string $keyId): APIKeyResponse
    {
        $data = $this->http->post('/api/v1/me/api-keys/' . rawurlencode($keyId) . '/regenerate');
        return APIKeyResponse::fromArray($data);
    }
}

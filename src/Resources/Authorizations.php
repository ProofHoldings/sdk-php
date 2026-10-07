<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Authorization;
use ProofHoldings\Types\AuthorizationListResponse;
use ProofHoldings\Types\CreateAuthorizationResponse;

class Authorizations
{
    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): CreateAuthorizationResponse
    {
        $data = $this->http->post('/api/v1/authorizations', $params);
        return CreateAuthorizationResponse::fromArray($data);
    }

    public function retrieve(string $id): Authorization
    {
        $data = $this->http->get('/api/v1/authorizations/' . rawurlencode($id));
        return Authorization::fromArray($data);
    }

    public function list(array $params = []): AuthorizationListResponse
    {
        $data = $this->http->get('/api/v1/authorizations', $params);
        return AuthorizationListResponse::fromArray($data);
    }

    public function revoke(string $id, array $params = []): Authorization
    {
        $data = $this->http->delete('/api/v1/authorizations/' . rawurlencode($id), $params);
        return Authorization::fromArray($data);
    }

    /** @return array<string, mixed> */
    public function export(array $params = []): array
    {
        return $this->http->get('/api/v1/authorizations/export', $params);
    }
}

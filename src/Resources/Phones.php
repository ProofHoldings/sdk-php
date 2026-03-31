<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\UserPhone;

class Phones
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all phones for the authenticated user. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/phones');
    }

    /** Remove a phone by ID. */
    public function remove(string $phoneId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/phones/' . rawurlencode($phoneId));
        return SuccessResponse::fromArray($data);
    }

    /** Set a phone as the primary phone. */
    public function setPrimary(string $phoneId): SuccessResponse
    {
        $data = $this->http->put('/api/v1/me/phones/' . rawurlencode($phoneId) . '/primary');
        return SuccessResponse::fromArray($data);
    }

    /** Start adding a new phone. */
    public function startAdd(array $params): array
    {
        return $this->http->post('/api/v1/me/phones/add', $params);
    }

    /** Get the status of a phone add session. */
    public function getAddStatus(string $sessionId): array
    {
        return $this->http->get('/api/v1/me/phones/add/' . rawurlencode($sessionId));
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Confirmation;
use ProofHoldings\Types\ConfirmationListResponse;

class Confirmations
{
    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): Confirmation
    {
        $data = $this->http->post('/api/v1/confirmations', $params);
        return Confirmation::fromArray($data);
    }

    public function retrieve(string $id): Confirmation
    {
        $data = $this->http->get('/api/v1/confirmations/' . rawurlencode($id));
        return Confirmation::fromArray($data);
    }

    public function list(array $params = []): ConfirmationListResponse
    {
        $data = $this->http->get('/api/v1/confirmations', $params);
        return ConfirmationListResponse::fromArray($data);
    }
}

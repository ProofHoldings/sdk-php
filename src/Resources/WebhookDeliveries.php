<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

class WebhookDeliveries
{
    public function __construct(private readonly HttpClient $http) {}

    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/webhook-deliveries', $params);
    }

    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/webhook-deliveries/' . rawurlencode($id));
    }

    public function retry(string $id): array
    {
        return $this->http->post('/api/v1/webhook-deliveries/' . rawurlencode($id) . '/retry');
    }
}

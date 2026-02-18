<?php

declare(strict_types=1);

namespace Proof\Resources;

use Proof\HttpClient;

class WebhookDeliveries
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get webhook delivery statistics (totals, rates, recent failures). */
    public function stats(): array
    {
        return $this->http->get('/api/v1/webhook-deliveries/stats');
    }

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

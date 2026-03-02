<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\RetryDeliveryResponse;
use ProofHoldings\Types\WebhookDelivery;
use ProofHoldings\Types\WebhookDeliveryListResponse;
use ProofHoldings\Types\WebhookDeliveryStats;

class WebhookDeliveries
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get webhook delivery statistics (totals, rates, recent failures). */
    public function stats(): WebhookDeliveryStats
    {
        $data = $this->http->get('/api/v1/webhook-deliveries/stats');
        return WebhookDeliveryStats::fromArray($data);
    }

    public function list(array $params = []): WebhookDeliveryListResponse
    {
        $data = $this->http->get('/api/v1/webhook-deliveries', $params);
        return WebhookDeliveryListResponse::fromArray($data);
    }

    public function retrieve(string $id): WebhookDelivery
    {
        $data = $this->http->get('/api/v1/webhook-deliveries/' . rawurlencode($id));
        return WebhookDelivery::fromArray($data);
    }

    public function retry(string $id): RetryDeliveryResponse
    {
        $data = $this->http->post('/api/v1/webhook-deliveries/' . rawurlencode($id) . '/retry');
        return RetryDeliveryResponse::fromArray($data);
    }
}

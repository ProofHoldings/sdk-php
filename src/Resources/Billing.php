<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\CheckoutResponse;
use ProofHoldings\Types\PortalResponse;

class Billing
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get current subscription details. */
    public function subscription(): array
    {
        return $this->http->get('/api/v1/billing/subscription');
    }

    /** Create a Stripe checkout session for plan upgrade. */
    public function checkout(array $params): CheckoutResponse
    {
        $data = $this->http->post('/api/v1/billing/checkout', $params);
        return CheckoutResponse::fromArray($data);
    }

    /** Create a Stripe customer portal session. */
    public function portal(array $params): PortalResponse
    {
        $data = $this->http->post('/api/v1/billing/portal', $params);
        return PortalResponse::fromArray($data);
    }
}

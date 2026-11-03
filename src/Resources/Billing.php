<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Billing — the subscription, Stripe checkout and portal links, and the prepaid balance.
 *
 * Checkout, portal and top-up throw BillingUnavailableException (`503 billing_unavailable`) while
 * billing is not live; the reads keep answering. Their redirect URLs must be on `proof.holdings` (or
 * `localhost`): the API refuses any other host, so a flow that sends the payer back to your own site
 * cannot use them yet. The four writes are account-level, so a key bound to one sub-account is
 * refused `403 account_key_required`.
 *
 * Returns plain arrays in the shape of the named OpenAPI components, like `delegations` and `circles`.
 */
class Billing
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * The account's subscription, whatever its status; with none, the account's plan and `status: null`.
     *
     * @return array<string, mixed> SubscriptionResponse
     */
    public function subscription(): array
    {
        return $this->http->get('/api/v1/billing/subscription');
    }

    /**
     * Open a Stripe Checkout session for a plan and return its `checkout_url`. Refused
     * `409 subscription_exists` while the account has a live subscription, and
     * `409 company_details_required` until its company profile is complete.
     * Its redirect URLs must be on `proof.holdings` (or `localhost`): the API refuses any other host.
     * A key bound to one sub-account is refused `403 account_key_required`.
     *
     * @param array<string, mixed> $params {plan, success_url, cancel_url}
     * @return array<string, mixed> CheckoutResponse
     */
    public function checkout(array $params): array
    {
        return $this->http->post('/api/v1/billing/checkout', $params);
    }

    /**
     * Open a Stripe billing portal session and return its `portal_url` (`404 no_subscription` without one).
     * Its redirect URLs must be on `proof.holdings` (or `localhost`): the API refuses any other host.
     * A key bound to one sub-account is refused `403 account_key_required`.
     *
     * @param array<string, mixed> $params {return_url}
     * @return array<string, mixed> PortalResponse
     */
    public function portal(array $params): array
    {
        return $this->http->post('/api/v1/billing/portal', $params);
    }

    /**
     * Open a Stripe Checkout session for a prepaid balance top-up and return its `checkout_url`.
     * `amount` is an int in euros: 10, 50 or 200. Nothing is credited here — the balance grows once
     * Stripe reports the payment. `auto_top_up: true` is refused to an API key (`403 owner_session_required`):
     * saving the card for automatic top-ups is the account owner's act on the dashboard.
     * Its redirect URLs must be on `proof.holdings` (or `localhost`): the API refuses any other host.
     * A key bound to one sub-account is refused `403 account_key_required`.
     *
     * @param array<string, mixed> $params {amount, success_url, cancel_url, auto_top_up?}
     * @return array<string, mixed> TopUpResponse
     */
    public function topUp(array $params): array
    {
        return $this->http->post('/api/v1/billing/top-up', $params);
    }

    /**
     * The prepaid balance, the low-balance threshold, the top-up amounts and the auto top-up setting.
     *
     * @return array<string, mixed> BalanceResponse
     */
    public function balance(): array
    {
        return $this->http->get('/api/v1/billing/balance');
    }

    /**
     * The balance history, newest first: every top-up, refund and adjustment, and one usage row per month.
     *
     * @param array<string, mixed> $params {page?, limit?}
     * @return array<string, mixed> LedgerListResponse
     */
    public function balanceLedger(array $params = []): array
    {
        return $this->http->get('/api/v1/billing/balance/ledger', $params);
    }

    /**
     * Switch auto top-up off (`['enabled' => false]`). An API key may not switch it on or change its amount
     * (`403 owner_session_required`): automatic top-ups charge the saved card with no one present, so the
     * account owner switches them on from the dashboard.
     * A key bound to one sub-account is refused `403 account_key_required`.
     *
     * @param array<string, mixed> $params {enabled, amount?}
     * @return array<string, mixed> BalanceResponse
     */
    public function autoTopUp(array $params): array
    {
        return $this->http->patch('/api/v1/billing/auto-top-up', $params);
    }
}

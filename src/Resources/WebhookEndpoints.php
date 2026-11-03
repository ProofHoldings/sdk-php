<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Webhook endpoints: the URLs your account's events are delivered to, each with its own
 * subscription (`events`), reach (`scope` — `all` sub-accounts, or the `selected` ones in `profile_ids`) and
 * signing secret. The signing secret is returned ONCE — by `create()` and by `rotateSecret()` —
 * and never by `list()`/`get()`/`update()`, which carry only `secret_set`. Store it when you get it.
 *
 * Methods return plain arrays, like `circles`/`delegations`/`profiles`. `Types.php` carries the
 * generated `WebhookEndpoint`/`WebhookEndpointList`/`WebhookEndpointWithSecret` DTOs, but every
 * `::fromArray()` return path must be held to a real controller response by a case in the root
 * drift suite `sdk-response-shapes.test.ts`; hydrating this resource is a separate change.
 */
class WebhookEndpoints
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * List the account's webhook endpoints (`{data: [...]}`; no secrets).
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->http->get('/api/v1/webhook-endpoints');
    }

    /**
     * Create a webhook endpoint (`url`, `scope`, optional `events`/`profile_ids`/`status`/`description`).
     * The response carries the signing secret in `secret` — returned only here and by `rotateSecret()`.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/api/v1/webhook-endpoints', $params);
    }

    /**
     * Get a webhook endpoint by ID (no secret; `secret_set` says whether one exists).
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->http->get('/api/v1/webhook-endpoints/' . rawurlencode($id));
    }

    /**
     * Update a webhook endpoint (any of `url`/`events`/`scope`/`profile_ids`/`status`/`description`).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function update(string $id, array $params): array
    {
        return $this->http->patch('/api/v1/webhook-endpoints/' . rawurlencode($id), $params);
    }

    /**
     * Delete a webhook endpoint (204, no body).
     */
    public function delete(string $id): void
    {
        $this->http->delete('/api/v1/webhook-endpoints/' . rawurlencode($id));
    }

    /**
     * Rotate an endpoint's signing secret. The new secret is returned in `secret` in this response
     * only; new deliveries are signed with it, while deliveries already queued keep the secret they
     * were queued with.
     *
     * @return array<string, mixed>
     */
    public function rotateSecret(string $id): array
    {
        return $this->http->post('/api/v1/webhook-endpoints/' . rawurlencode($id) . '/rotate-secret');
    }
}

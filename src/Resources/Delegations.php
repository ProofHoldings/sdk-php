<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Delegations — Proof of Delegation. A control-proven domain authorizes a typed
 * url/purl artifact for a set of capability scopes; the attestation is that the
 * domain's controller authorized the artifact, nothing more.
 *
 * Responses are returned as raw arrays: the generated Types.php has no
 * DelegationWithToken/DelegationDetail classes (allOf compositions are dropped
 * by the generator) and hydrating the base Delegation would silently lose
 * token/effective_status/is_valid.
 */
class Delegations
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a delegation from your active domain control proof (each mint is a billable proof).
     *
     * The result carries the publishable token AND the derived effective_status/is_valid the
     * reads carry — the shape of a delegation does not depend on which verb produced it.
     *
     * @param array<string, mixed> $params {control_proof, delegate: {type, value}, scope[], expires_in?}
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/api/v1/delegations', $params);
    }

    /**
     * Get a delegation by Mongo id or ph_dlg_* handle — returns the stored token and derived status.
     *
     * @return array<string, mixed>
     */
    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/delegations/' . rawurlencode($id));
    }

    /**
     * List your own delegations (tokens are not included in the list).
     *
     * Each item carries the DERIVED `effective_status`/`is_valid` beside its stored `status` —
     * render the derived one. The `status` filter matches the stored record only.
     *
     * @param array<string, mixed> $params {page?, limit?, status?}
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/delegations', $params);
    }

    /**
     * Revoke one delegation — the domain proof and sibling delegations stay valid.
     *
     * Idempotent: a repeat revoke answers 200 with the same body, here and on
     * POST /proofs/{handle}/revoke. (A PROOF differs deliberately — that one answers
     * 400 already_revoked.) The result carries the derived effective_status/is_valid.
     *
     * @param array<string, mixed> $params {reason?: user_request|fraud|key_compromise|other}
     * @return array<string, mixed>
     */
    public function revoke(string $id, array $params = []): array
    {
        // An empty array must not become the JSON body "[]" — the route's Zod
        // validator is an object schema and rejects arrays; no body parses as {}.
        return $this->http->post('/api/v1/delegations/' . rawurlencode($id) . '/revoke', $params === [] ? null : $params);
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Hitl as HitlConfig;
use ProofHoldings\Types\HitlAuthorizationResponse;
use ProofHoldings\Types\HitlFinalizeResponse;
use ProofHoldings\Types\HitlListResponse;

/**
 * HITL config CRUD. Configs are created in `pending` status; upload an
 * encryption keypair (see HitlKeys) and call finalize() to activate.
 */
class Hitl
{
    public function __construct(private readonly HttpClient $http) {}

    /** Create a new HITL configuration (starts in `pending` status). */
    public function create(array $params): HitlConfig
    {
        $data = $this->http->post('/api/v1/hitl', $params);
        return HitlConfig::fromArray($data);
    }

    /** List HITL configurations with optional filters. */
    public function list(array $params = []): HitlListResponse
    {
        $data = $this->http->get('/api/v1/hitl', $params);
        return HitlListResponse::fromArray($data);
    }

    /** Get a HITL configuration by ID. */
    public function get(string $id): HitlConfig
    {
        $data = $this->http->get('/api/v1/hitl/' . rawurlencode($id));
        return HitlConfig::fromArray($data);
    }

    /** Update a HITL configuration's name, channels, or timeout. */
    public function update(string $id, array $params): HitlConfig
    {
        $data = $this->http->patch('/api/v1/hitl/' . rawurlencode($id), $params);
        return HitlConfig::fromArray($data);
    }

    /** Archive a HITL configuration (soft delete; returns the archived config). */
    public function archive(string $id): HitlConfig
    {
        $data = $this->http->delete('/api/v1/hitl/' . rawurlencode($id));
        return HitlConfig::fromArray($data);
    }

    /**
     * Finalize a pending HITL configuration (pending → active). Requires an
     * uploaded encryption keypair; dispatches authorization consent messages.
     */
    public function finalize(string $id): HitlFinalizeResponse
    {
        $data = $this->http->post('/api/v1/hitl/' . rawurlencode($id) . '/finalize');
        return HitlFinalizeResponse::fromArray($data);
    }

    /**
     * Request authorization consent for channels in an active HITL config
     * that don't have one yet.
     */
    public function requestAuthorization(string $id): HitlAuthorizationResponse
    {
        $data = $this->http->post('/api/v1/hitl/' . rawurlencode($id) . '/authorize');
        return HitlAuthorizationResponse::fromArray($data);
    }

    /**
     * List enrolled approver public keys for a config. Feed these into
     * HitlCrypto::encryptMessageV2 to wrap a confirmation for every approver.
     *
     * @return array<string, mixed>
     */
    public function getApproverKeys(string $id): array
    {
        return $this->http->get('/api/v1/hitl/' . rawurlencode($id) . '/approver-keys');
    }
}

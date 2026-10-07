<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

class HitlKeys
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get encryption keypair for a HITL config. */
    public function get(string $hitlId): array
    {
        return $this->http->get('/api/v1/hitl/' . rawurlencode($hitlId) . '/keys');
    }

    /** Upload encryption keypair for a HITL config. */
    public function upload(string $hitlId, array $params): array
    {
        return $this->http->put('/api/v1/hitl/' . rawurlencode($hitlId) . '/keys', $params);
    }

    /** Delete encryption keypair from a HITL config. */
    public function delete(string $hitlId): array
    {
        return $this->http->delete('/api/v1/hitl/' . rawurlencode($hitlId) . '/keys');
    }
}

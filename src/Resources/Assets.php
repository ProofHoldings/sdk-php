<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\UserAsset;

class Assets
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all verified assets for the authenticated user. */
    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/me/assets', $params);
    }

    /** Get a specific asset by ID. */
    public function get(string $assetId): UserAsset
    {
        $data = $this->http->get('/api/v1/me/assets/' . rawurlencode($assetId));
        return UserAsset::fromArray($data);
    }

    /** Revoke an asset by ID. */
    public function revoke(string $assetId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/assets/' . rawurlencode($assetId));
        return SuccessResponse::fromArray($data);
    }
}

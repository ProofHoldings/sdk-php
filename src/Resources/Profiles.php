<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\PublicProfile;
use ProofHoldings\Types\SuccessResponse;

class Profiles
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all profiles for the authenticated user. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/profiles');
    }

    /** Create a new profile. */
    public function create(array $params): PublicProfile
    {
        $data = $this->http->post('/api/v1/me/profiles', $params);
        return PublicProfile::fromArray($data);
    }

    /** Get a specific profile by ID. */
    public function retrieve(string $profileId): PublicProfile
    {
        $data = $this->http->get('/api/v1/me/profiles/' . rawurlencode($profileId));
        return PublicProfile::fromArray($data);
    }

    /** Update a specific profile. */
    public function update(string $profileId, array $params): PublicProfile
    {
        $data = $this->http->patch('/api/v1/me/profiles/' . rawurlencode($profileId), $params);
        return PublicProfile::fromArray($data);
    }

    /** Delete a profile. */
    public function delete(string $profileId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/profiles/' . rawurlencode($profileId));
        return SuccessResponse::fromArray($data);
    }

    /** Set a profile as the primary profile. */
    public function setPrimary(string $profileId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/profiles/' . rawurlencode($profileId) . '/primary');
        return SuccessResponse::fromArray($data);
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\PublicProfile;
use ProofHoldings\Types\SuccessResponse;

class PublicProfiles
{
    public function __construct(private readonly HttpClient $http) {}

    // --- Public endpoints ---

    /** Get public profile by profile ID. */
    public function getById(string $profileId): PublicProfile
    {
        $data = $this->http->get('/api/v1/profiles/p/' . rawurlencode($profileId));
        return PublicProfile::fromArray($data);
    }

    /** Get profile avatar image. */
    public function getAvatar(string $profileId): array
    {
        return $this->http->get('/api/v1/profiles/p/' . rawurlencode($profileId) . '/avatar');
    }

    /** Get public profile by username. */
    public function getByUsername(string $username): PublicProfile
    {
        $data = $this->http->get('/api/v1/profiles/u/' . rawurlencode($username));
        return PublicProfile::fromArray($data);
    }

    /** Check username availability. */
    public function checkUsername(string $username): array
    {
        return $this->http->get('/api/v1/profiles/check-username/' . rawurlencode($username));
    }

    // --- Multi-profile endpoints ---

    /** List all profiles. */
    public function listProfiles(): array
    {
        return $this->http->get('/api/v1/profiles/profiles');
    }

    /** Create a new profile. */
    public function createProfile(array $params): PublicProfile
    {
        $data = $this->http->post('/api/v1/profiles/profiles', $params);
        return PublicProfile::fromArray($data);
    }

    /** Get a specific profile by ID. */
    public function getProfile(string $profileId): PublicProfile
    {
        $data = $this->http->get('/api/v1/profiles/profiles/' . rawurlencode($profileId));
        return PublicProfile::fromArray($data);
    }

    /** Update a specific profile. */
    public function updateProfile(string $profileId, array $params): PublicProfile
    {
        $data = $this->http->patch('/api/v1/profiles/profiles/' . rawurlencode($profileId), $params);
        return PublicProfile::fromArray($data);
    }

    /** Delete a profile. */
    public function deleteProfile(string $profileId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/profiles/profiles/' . rawurlencode($profileId));
        return SuccessResponse::fromArray($data);
    }

    /** Set a profile as primary. */
    public function setPrimary(string $profileId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/profiles/profiles/' . rawurlencode($profileId) . '/primary');
        return SuccessResponse::fromArray($data);
    }

    /** Update proofs for a specific profile. */
    public function updateProfileProofs(string $profileId, array $params): PublicProfile
    {
        $data = $this->http->put('/api/v1/profiles/profiles/' . rawurlencode($profileId) . '/proofs', $params);
        return PublicProfile::fromArray($data);
    }

    // --- Legacy /me endpoints ---

    /** Get current user's primary profile. */
    public function getMyProfile(): PublicProfile
    {
        $data = $this->http->get('/api/v1/profiles/me');
        return PublicProfile::fromArray($data);
    }

    /** Update current user's profile. */
    public function updateMyProfile(array $params): PublicProfile
    {
        $data = $this->http->put('/api/v1/profiles/me', $params);
        return PublicProfile::fromArray($data);
    }

    /** Claim a username. */
    public function claimUsername(array $params): PublicProfile
    {
        $data = $this->http->post('/api/v1/profiles/me/username', $params);
        return PublicProfile::fromArray($data);
    }

    /** Get available assets for public profile. */
    public function getAvailableAssets(): array
    {
        return $this->http->get('/api/v1/profiles/me/assets');
    }

    /** Update public proofs. */
    public function updatePublicProofs(array $params): PublicProfile
    {
        $data = $this->http->put('/api/v1/profiles/me/proofs', $params);
        return PublicProfile::fromArray($data);
    }
}

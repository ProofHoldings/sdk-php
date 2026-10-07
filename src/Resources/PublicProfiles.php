<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Public profile reads — unauthenticated endpoints under /api/v1/profiles;
 * the client's Authorization header is sent but ignored by the server.
 */
class PublicProfiles
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Get a public profile by its PUBLIC profile_id (the profile_id field
     * on /me/profiles responses) — the internal id is NOT accepted by this
     * endpoint and will 404.
     */
    public function getById(string $profileId): array
    {
        return $this->http->get('/api/v1/profiles/p/' . rawurlencode($profileId));
    }

    /**
     * Get a profile's avatar image. Accepts either the public profile_id or
     * the internal id. Returns ['data' => binary string,
     * 'content_type' => string]; https-hosted avatars are followed through
     * the server's 301 redirect.
     */
    public function getAvatar(string $profileId): array
    {
        return $this->http->get('/api/v1/profiles/p/' . rawurlencode($profileId) . '/avatar');
    }

    /** Get a public profile by username. */
    public function getByUsername(string $username): array
    {
        return $this->http->get('/api/v1/profiles/u/' . rawurlencode($username));
    }

    /**
     * Get the embeddable badge descriptor for a username.
     *
     * Everything needed to embed the verification badge in one call: the subject it
     * asserts, the live verified count, the badge image URL for each style, and
     * ready-to-paste snippets. A username with no public record answers 200 with
     * state "no_public_record" rather than a 404, so a caller never has to treat
     * "nothing to show" as an error.
     */
    public function getBadge(string $username): array
    {
        return $this->http->get('/api/v1/profiles/badge/' . rawurlencode($username));
    }

    /** Check username availability. */
    public function checkUsername(string $username): array
    {
        return $this->http->get('/api/v1/profiles/check-username/' . rawurlencode($username));
    }
}

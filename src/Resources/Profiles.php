<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Multi-brand public profiles under /api/v1/me/profiles: profile CRUD and
 * primary selection. The per-profile template methods are deprecated: a
 * sub-account's templates live on `templates`, named by its `$profileId`.
 * Responses are returned as plain arrays (no generated DTO wrappers exist
 * for the profile envelopes).
 */
class Profiles
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all profiles for the authenticated user. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/profiles');
    }

    /** Create a new profile. */
    public function create(array $params = []): array
    {
        return $this->http->post('/api/v1/me/profiles', $params);
    }

    /** Get a profile by ID. */
    public function get(string $profileId): array
    {
        return $this->http->get('/api/v1/me/profiles/' . rawurlencode($profileId));
    }

    /** Update a profile. */
    public function update(string $profileId, array $params): array
    {
        return $this->http->patch('/api/v1/me/profiles/' . rawurlencode($profileId), $params);
    }

    /** Delete a profile. */
    public function delete(string $profileId): array
    {
        return $this->http->delete('/api/v1/me/profiles/' . rawurlencode($profileId));
    }

    /** Set a profile as the primary profile. */
    public function setPrimary(string $profileId): array
    {
        return $this->http->post('/api/v1/me/profiles/' . rawurlencode($profileId) . '/primary');
    }

    /**
     * Rotate a sub-account's callback signing secret (`{profile_id, secret}`). The new secret is
     * returned in this response only; deliveries already queued keep the secret they were queued
     * with. An API key needs the
     * `webhooks:write` scope and may rotate only the sub-account it is bound to.
     *
     * @return array<string, mixed>
     */
    public function rotateCallbackSecret(string $profileId): array
    {
        return $this->http->post('/api/v1/profiles/profiles/' . rawurlencode($profileId) . '/callback-secret/rotate');
    }

    /**
     * List a profile's message templates (custom and default).
     *
     * @deprecated The route is retired and answers 410 `endpoint_retired`;
     *             use `templates->list($profileId)`.
     *             Removed in the next major.
     */
    public function listTemplates(string $profileId): array
    {
        return $this->http->get('/api/v1/me/profiles/' . rawurlencode($profileId) . '/templates');
    }

    /**
     * Create or update a profile template for a channel/message type.
     * NOTE: the http call must stay on ONE line — parsePhpSdkMethods only
     * matches `$this->http->verb(...)` with the path on the same line.
     *
     * @deprecated The route is retired and answers 410 `endpoint_retired`;
     *             use `templates->upsert($channel, $type, $content, $profileId)`.
     *             Removed in the next major.
     */
    public function updateTemplate(string $profileId, string $channel, string $type, array $content): array
    {
        return $this->http->put('/api/v1/me/profiles/' . rawurlencode($profileId) . '/templates/' . rawurlencode($channel) . '/' . rawurlencode($type), $content);
    }

    /**
     * Delete a profile template (resets to default). Single-line call — see updateTemplate.
     *
     * @deprecated The route is retired and answers 410 `endpoint_retired`;
     *             use `templates->delete($channel, $type, $profileId)`.
     *             Removed in the next major.
     */
    public function deleteTemplate(string $profileId, string $channel, string $type): array
    {
        return $this->http->delete('/api/v1/me/profiles/' . rawurlencode($profileId) . '/templates/' . rawurlencode($channel) . '/' . rawurlencode($type));
    }

    /**
     * Preview a profile template with the profile's branding. Single-line call — see updateTemplate.
     *
     * @deprecated The route is retired and answers 410 `endpoint_retired`;
     *             use `templates->preview($params, $profileId)`.
     *             Removed in the next major.
     */
    public function previewTemplate(string $profileId, array $params): array
    {
        return $this->http->post('/api/v1/me/profiles/' . rawurlencode($profileId) . '/templates/preview', $params);
    }
}

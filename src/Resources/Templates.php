<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Message templates: custom notification templates per channel/message
 * type, plus preview and render helpers. Responses are returned as plain
 * arrays (no generated DTO wrappers exist for the template envelopes).
 *
 * Templates belong to a sub-account. Every method except
 * getDefaults takes an optional trailing `$profileId`, sent as the
 * `profile_id` query parameter and omitted when null. Without it a
 * sub-account key uses its own sub-account and an account-wide key the
 * account's primary one. A sub-account key naming another sub-account gets
 * 403 `profile_mismatch`; a sub-account the account does not own, 403
 * `profile_not_owned`; a malformed id, 400 `invalid_profile_id`.
 */
class Templates
{
    public function __construct(private readonly HttpClient $http) {}

    /** List the sub-account's custom templates plus available channels, message types, and variables. */
    public function list(?string $profileId = null): array
    {
        return $this->http->get('/api/v1/templates', self::projectQuery($profileId));
    }

    /** Get all default templates (for reference). */
    public function getDefaults(): array
    {
        return $this->http->get('/api/v1/templates/defaults');
    }

    /** Get a specific template (the sub-account's custom one if set, default otherwise); `scope` is `project` or `default`. */
    public function get(string $channel, string $messageType, ?string $profileId = null): array
    {
        return $this->http->get('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType), self::projectQuery($profileId));
    }

    /**
     * Create or update the sub-account's custom template. A template names its
     * brand only through `{{business_name}}`: text containing the name of any
     * of the account's sub-accounts gets 400 `template_hardcoded_name`.
     */
    public function upsert(string $channel, string $messageType, array $content, ?string $profileId = null): array
    {
        return $this->http->put('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType), $content, self::projectQuery($profileId));
    }

    /** Delete the sub-account's custom template (resets to default; 404 if no custom template exists). */
    public function delete(string $channel, string $messageType, ?string $profileId = null): array
    {
        return $this->http->delete('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType), null, self::projectQuery($profileId));
    }

    /** Preview a template with sample data and the sub-account's branding. */
    public function preview(array $params, ?string $profileId = null): array
    {
        return $this->http->post('/api/v1/templates/preview', $params, self::projectQuery($profileId));
    }

    /** Render a template with provided variables (for testing). */
    public function render(array $params, ?string $profileId = null): array
    {
        return $this->http->post('/api/v1/templates/render', $params, self::projectQuery($profileId));
    }

    /** @return array<string, string> */
    private static function projectQuery(?string $profileId): array
    {
        return $profileId === null ? [] : ['profile_id' => $profileId];
    }
}

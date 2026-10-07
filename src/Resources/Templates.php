<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Message templates: custom notification templates per channel/message
 * type, plus preview and render helpers. Responses are returned as plain
 * arrays (no generated DTO wrappers exist for the template envelopes).
 */
class Templates
{
    public function __construct(private readonly HttpClient $http) {}

    /** List custom templates plus available channels, message types, and variables. */
    public function list(): array
    {
        return $this->http->get('/api/v1/templates');
    }

    /** Get all default templates (for reference). */
    public function getDefaults(): array
    {
        return $this->http->get('/api/v1/templates/defaults');
    }

    /** Get a specific template (custom if set, default otherwise). */
    public function get(string $channel, string $messageType): array
    {
        return $this->http->get('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType));
    }

    /** Create or update a custom template. */
    public function upsert(string $channel, string $messageType, array $content): array
    {
        return $this->http->put('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType), $content);
    }

    /** Delete a custom template (resets to default; 404 if no custom template exists). */
    public function delete(string $channel, string $messageType): array
    {
        return $this->http->delete('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType));
    }

    /** Preview a template with sample data. */
    public function preview(array $params): array
    {
        return $this->http->post('/api/v1/templates/preview', $params);
    }

    /** Render a template with provided variables (for testing). */
    public function render(array $params): array
    {
        return $this->http->post('/api/v1/templates/render', $params);
    }
}

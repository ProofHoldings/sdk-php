<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\Template;

class Templates
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all custom templates for the authenticated tenant. */
    public function list(): array
    {
        return $this->http->get('/api/v1/templates');
    }

    /** Get all default templates. */
    public function getDefaults(): array
    {
        return $this->http->get('/api/v1/templates/defaults');
    }

    /** Get a specific template (custom or default) by channel and message type. */
    public function retrieve(string $channel, string $messageType): Template
    {
        $data = $this->http->get('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType));
        return Template::fromArray($data);
    }

    /** Create or update a custom template. */
    public function upsert(string $channel, string $messageType, array $params): Template
    {
        $data = $this->http->put('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType), $params);
        return Template::fromArray($data);
    }

    /** Delete a custom template (resets to default). */
    public function delete(string $channel, string $messageType): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType));
        return SuccessResponse::fromArray($data);
    }

    /** Preview a template with sample data. */
    public function preview(array $params): array
    {
        return $this->http->post('/api/v1/templates/preview', $params);
    }

    /** Render a template with provided variables. */
    public function render(array $params): array
    {
        return $this->http->post('/api/v1/templates/render', $params);
    }
}

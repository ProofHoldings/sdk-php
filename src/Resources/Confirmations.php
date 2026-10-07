<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Streaming;
use ProofHoldings\Types\ApprovalLinkResponse;
use ProofHoldings\Types\Confirmation;
use ProofHoldings\Types\ConfirmationListResponse;

class Confirmations
{
    private const array TERMINAL_STATES = ['approved', 'denied', 'expired', 'cancelled'];

    public function __construct(private readonly HttpClient $http) {}

    /**
     * Stream confirmation status via SSE with polling fallback.
     * Endpoint: GET /api/v1/confirmations/:id/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function stream(string $id, ?callable $shouldCancel = null): \Generator
    {
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/confirmations/' . rawurlencode($id) . '/events',
            fn () => (array) $this->http->get('/api/v1/confirmations/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Confirmation {$id}",
            $shouldCancel,
        );
    }

    public function create(array $params): Confirmation
    {
        $data = $this->http->post('/api/v1/confirmations', $params);
        return Confirmation::fromArray($data);
    }

    public function retrieve(string $id): Confirmation
    {
        $data = $this->http->get('/api/v1/confirmations/' . rawurlencode($id));
        return Confirmation::fromArray($data);
    }

    public function list(array $params = []): ConfirmationListResponse
    {
        $data = $this->http->get('/api/v1/confirmations', $params);
        return ConfirmationListResponse::fromArray($data);
    }

    /** Generate a fresh HMAC approval link for a pending confirmation. */
    public function approvalLink(string $id): ApprovalLinkResponse
    {
        $data = $this->http->post('/api/v1/confirmations/' . rawurlencode($id) . '/approval-link');
        return ApprovalLinkResponse::fromArray($data);
    }
}

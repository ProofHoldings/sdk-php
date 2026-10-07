<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Streaming;
use ProofHoldings\Types\ChatIdDiscoveryResponse;

/**
 * HITL Telegram chat-id discovery streaming. Distinct from HitlKeys (E2E
 * encryption key management) — both share the same underlying HttpClient
 * but live as separate resource classes because they cover different
 * lifecycle stages.
 */
class HitlChatId
{
    public function __construct(private readonly HttpClient $http) {}

    /** Create a chat-ID discovery token (Telegram deeplink + QR code). */
    public function create(): ChatIdDiscoveryResponse
    {
        $data = $this->http->post('/api/v1/hitl/chat-id-discovery');
        return ChatIdDiscoveryResponse::fromArray($data);
    }

    /**
     * Stream chat-id discovery progress via SSE with polling fallback.
     * Endpoint: GET /api/v1/hitl/chat-id-discovery/:token/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function streamDiscovery(string $token, ?callable $shouldCancel = null): \Generator
    {
        $tok = rawurlencode($token);
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/hitl/chat-id-discovery/' . $tok . '/events',
            fn () => $this->http->get('/api/v1/hitl/chat-id-discovery/' . $tok),
            ['completed', 'expired'],
            "HITL chat-id discovery {$token}",
            $shouldCancel,
        );
    }
}

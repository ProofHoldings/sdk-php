<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Streaming;

/**
 * Auth-session streaming. Auth sessions use the session ID itself as the
 * bearer secret (format: ``auth_[a-f0-9]{32}``) — the SDK forwards the
 * standard API-key Authorization header, the server gates access on the
 * session ID format + ownership.
 */
class AuthSessions
{
    private const array TERMINAL_STATES = ['verified', 'failed', 'expired'];

    public function __construct(private readonly HttpClient $http) {}

    /**
     * Stream auth-session status via SSE with polling fallback.
     * Endpoint: GET /api/v1/auth/sessions/:id/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function stream(string $id, ?callable $shouldCancel = null): \Generator
    {
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/auth/sessions/' . rawurlencode($id) . '/events',
            fn () => $this->http->get('/api/v1/sessions/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Auth session {$id}",
            $shouldCancel,
        );
    }
}

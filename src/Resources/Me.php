<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Streaming;

/**
 * Dashboard self-service flows: 2FA challenges, add-phone, add-email. These
 * endpoints are JWT-authenticated server-side; API-key clients will receive
 * a 401 and the generator will throw NetworkException unless polling
 * fallback is configured (it is, against the same JWT-protected resource).
 */
class Me
{
    private const array SESSION_TERMINAL_STATES = ['verified', 'failed', 'expired'];

    public function __construct(private readonly HttpClient $http) {}

    /**
     * Stream a 2FA session.
     * Endpoint: GET /api/v1/me/2fa/:sessionId/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function stream2FASession(string $sessionId, ?callable $shouldCancel = null): \Generator
    {
        $sid = rawurlencode($sessionId);
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/me/2fa/' . $sid . '/events',
            fn () => $this->http->get('/api/v1/me/2fa/' . $sid),
            self::SESSION_TERMINAL_STATES,
            "2FA session {$sessionId}",
            $shouldCancel,
        );
    }

    /**
     * Stream an add-phone session.
     * Endpoint: GET /api/v1/me/phones/add/:sessionId/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function streamPhoneAddSession(string $sessionId, ?callable $shouldCancel = null): \Generator
    {
        $sid = rawurlencode($sessionId);
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/me/phones/add/' . $sid . '/events',
            fn () => $this->http->get('/api/v1/me/phones/add/' . $sid),
            self::SESSION_TERMINAL_STATES,
            "Phone-add session {$sessionId}",
            $shouldCancel,
        );
    }

    /**
     * Stream an add-email session.
     * Endpoint: GET /api/v1/me/emails/add/:sessionId/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function streamEmailAddSession(string $sessionId, ?callable $shouldCancel = null): \Generator
    {
        $sid = rawurlencode($sessionId);
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/me/emails/add/' . $sid . '/events',
            fn () => $this->http->get('/api/v1/me/emails/add/' . $sid),
            self::SESSION_TERMINAL_STATES,
            "Email-add session {$sessionId}",
            $shouldCancel,
        );
    }

    // --- Dashboard / agent data surface (dual-auth: JWT or API key) ---

    /**
     * One-call account snapshot: quota + EoM projection, request health,
     * channel stats, HITL counts, active keys.
     * Endpoint: GET /api/v1/me/summary
     */
    public function summary(?string $environment = null): array
    {
        $query = $environment !== null ? ['environment' => $environment] : [];
        return $this->http->get('/api/v1/me/summary', $query);
    }

    /**
     * Prefix-anchored global account search across six resource buckets.
     * Endpoint: GET /api/v1/me/search
     */
    public function search(string $q, ?string $type = null, ?string $environment = null, ?int $limit = null): array
    {
        $query = ['q' => $q];
        if ($type !== null) {
            $query['type'] = $type;
        }
        if ($environment !== null) {
            $query['environment'] = $environment;
        }
        if ($limit !== null) {
            $query['limit'] = $limit;
        }
        return $this->http->get('/api/v1/me/search', $query);
    }

    /**
     * Retrieve the calling API key's own redacted metadata (agent self-inspection).
     * Endpoint: GET /api/v1/me/api-key
     */
    public function apiKeySelf(): array
    {
        return $this->http->get('/api/v1/me/api-key');
    }

    /**
     * Per-API-key usage counts (verifications/requests/confirmations/authorizations)
     * plus attribution context, for the key and every key it replaced
     * (api_key.replaced_key_ids).
     * Endpoint: GET /api/v1/me/api-keys/:id/usage
     */
    public function apiKeyUsage(string $id, ?string $environment = null): array
    {
        $query = $environment !== null ? ['environment' => $environment] : [];
        return $this->http->get('/api/v1/me/api-keys/' . rawurlencode($id) . '/usage', $query);
    }
}

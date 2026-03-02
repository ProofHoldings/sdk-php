<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Domain;
use ProofHoldings\Types\SuccessResponse;

class Domains
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all domains. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/domains');
    }

    /** Add a domain. */
    public function add(array $params): Domain
    {
        $data = $this->http->post('/api/v1/me/domains', $params);
        return Domain::fromArray($data);
    }

    /** Get a domain by ID. */
    public function get(string $domainId): Domain
    {
        $data = $this->http->get('/api/v1/me/domains/' . rawurlencode($domainId));
        return Domain::fromArray($data);
    }

    /** Delete a domain. */
    public function delete(string $domainId): array
    {
        return $this->http->delete('/api/v1/me/domains/' . rawurlencode($domainId));
    }

    /** Get OAuth URL for DNS provider authorization. */
    public function oauthUrl(string $domainId): array
    {
        return $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/oauth-url');
    }

    /** Verify domain ownership. */
    public function verify(string $domainId): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/verify');
        return Domain::fromArray($data);
    }

    /** Connect Cloudflare to a domain. */
    public function connectCloudflare(string $domainId, array $params): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/connect/cloudflare', $params);
        return Domain::fromArray($data);
    }

    /** Connect GoDaddy to a domain. */
    public function connectGoDaddy(string $domainId, array $params): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/connect/godaddy', $params);
        return Domain::fromArray($data);
    }

    /** Connect a DNS provider to a domain. */
    public function connectProvider(string $domainId, string $provider, array $params): Domain
    {
        $data = $this->http->post(
            '/api/v1/me/domains/' . rawurlencode($domainId) . '/connect/' . rawurlencode($provider),
            $params
        );
        return Domain::fromArray($data);
    }

    /** Add an additional verification provider to an already-verified domain. */
    public function addProvider(string $domainId, string $provider, array $params): Domain
    {
        $data = $this->http->post(
            '/api/v1/me/domains/' . rawurlencode($domainId) . '/add-provider/' . rawurlencode($provider),
            $params
        );
        return Domain::fromArray($data);
    }

    /** Get metadata for all supported DNS providers. */
    public function getProviders(): array
    {
        return $this->http->get('/api/v1/me/dns-providers');
    }

    /** Verify domain with existing credentials. */
    public function verifyWithCredentials(string $domainId): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/verify-with-credentials');
        return Domain::fromArray($data);
    }

    /** Check if credentials have access to domain. */
    public function checkCredentials(string $domainId): array
    {
        return $this->http->get('/api/v1/me/domains/' . rawurlencode($domainId) . '/check-credentials');
    }

    /** Start email verification for a domain. */
    public function startEmailVerification(string $domainId, array $params = []): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/verify-email', $params);
        return SuccessResponse::fromArray($data);
    }

    /** Confirm email verification code. */
    public function confirmEmailCode(string $domainId, array $params): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/verify-email/confirm', $params);
        return Domain::fromArray($data);
    }

    /** Resend email verification. */
    public function resendEmail(string $domainId, array $params = []): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/verify-email/resend', $params);
        return SuccessResponse::fromArray($data);
    }

    /** Setup email sending for a domain. */
    public function emailSetup(string $domainId, array $params = []): Domain
    {
        $data = $this->http->post('/api/v1/me/domains/' . rawurlencode($domainId) . '/email-setup', $params);
        return Domain::fromArray($data);
    }

    /** Check email sending status for a domain. */
    public function emailStatus(string $domainId): array
    {
        return $this->http->get('/api/v1/me/domains/' . rawurlencode($domainId) . '/email-status');
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\DNSProviderCredentials;
use ProofHoldings\Types\SuccessResponse;

class DnsCredentials
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all DNS credentials. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/dns-credentials');
    }

    /** Create a DNS credential. */
    public function create(array $params): DNSProviderCredentials
    {
        $data = $this->http->post('/api/v1/me/dns-credentials', $params);
        return DNSProviderCredentials::fromArray($data);
    }

    /** Delete a DNS credential. */
    public function delete(string $credentialId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/dns-credentials/' . rawurlencode($credentialId));
        return SuccessResponse::fromArray($data);
    }
}

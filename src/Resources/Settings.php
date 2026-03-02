<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

class Settings
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get user settings. */
    public function get(): array
    {
        return $this->http->get('/api/v1/me/settings');
    }

    /** Update user settings. */
    public function update(array $params): array
    {
        return $this->http->patch('/api/v1/me/settings', $params);
    }

    /** Get usage metrics. */
    public function getUsage(array $params = []): array
    {
        return $this->http->get('/api/v1/me/usage', $params);
    }

    /** Export user data (GDPR). */
    public function export(): array
    {
        return $this->http->get('/api/v1/me/export');
    }
}

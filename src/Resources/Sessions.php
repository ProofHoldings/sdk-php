<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;

class Sessions
{
    private const array TERMINAL_STATES = Polling::SESSION_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): array
    {
        return $this->http->post('/api/v1/sessions', $params);
    }

    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/sessions/' . rawurlencode($id));
    }

    public function waitForCompletion(
        string $id,
        float $interval = Polling::DEFAULT_INTERVAL,
        float $timeout = Polling::DEFAULT_TIMEOUT,
        float $backoff = Polling::DEFAULT_BACKOFF,
        float $maxInterval = Polling::DEFAULT_MAX_INTERVAL,
        float $jitter = Polling::DEFAULT_JITTER,
        ?callable $shouldCancel = null,
    ): array {
        return Polling::waitUntilComplete(
            fn () => $this->retrieve($id),
            self::TERMINAL_STATES,
            "Session {$id}",
            $interval,
            $timeout,
            $backoff,
            $maxInterval,
            $jitter,
            $shouldCancel,
        );
    }
}

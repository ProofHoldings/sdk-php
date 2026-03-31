<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;
use ProofHoldings\Types\Session;

class Sessions
{
    private const array TERMINAL_STATES = Polling::SESSION_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): Session
    {
        $data = $this->http->post('/api/v1/sessions', $params);
        return Session::fromArray($data);
    }

    public function retrieve(string $id): Session
    {
        $data = $this->http->get('/api/v1/sessions/' . rawurlencode($id));
        return Session::fromArray($data);
    }

    public function waitForCompletion(string $id, float $interval = Polling::DEFAULT_INTERVAL, float $timeout = Polling::DEFAULT_TIMEOUT): Session
    {
        $data = Polling::waitUntilComplete(
            fn () => $this->http->get('/api/v1/sessions/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Session {$id}",
            $interval,
            $timeout,
        );
        return Session::fromArray($data);
    }
}

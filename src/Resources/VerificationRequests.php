<?php

declare(strict_types=1);

namespace Proof\Resources;

use Proof\HttpClient;
use Proof\Polling;

class VerificationRequests
{
    private const array TERMINAL_STATES = Polling::REQUEST_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): array
    {
        return $this->http->post('/api/v1/verification-requests', $params);
    }

    public function retrieve(string $id): array
    {
        return $this->http->get('/api/v1/verification-requests/' . rawurlencode($id));
    }

    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/verification-requests', $params);
    }

    /** Get a verification request by its reference ID. */
    public function getByReference(string $referenceId): array
    {
        return $this->http->get('/api/v1/verification-requests/by-reference/' . rawurlencode($referenceId));
    }

    public function cancel(string $id): array
    {
        return $this->http->delete('/api/v1/verification-requests/' . rawurlencode($id));
    }

    public function waitForCompletion(string $id, float $interval = Polling::DEFAULT_INTERVAL, float $timeout = Polling::DEFAULT_TIMEOUT): array
    {
        return Polling::waitUntilComplete(
            fn () => $this->retrieve($id),
            self::TERMINAL_STATES,
            "Verification request {$id}",
            $interval,
            $timeout,
        );
    }
}

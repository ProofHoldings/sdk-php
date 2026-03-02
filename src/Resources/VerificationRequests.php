<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Polling;
use ProofHoldings\Types\CancelRequestResponse;
use ProofHoldings\Types\VerificationRequest;
use ProofHoldings\Types\VerificationRequestListResponse;

class VerificationRequests
{
    private const array TERMINAL_STATES = Polling::REQUEST_TERMINAL_STATES;

    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): VerificationRequest
    {
        $data = $this->http->post('/api/v1/verification-requests', $params);
        return VerificationRequest::fromArray($data);
    }

    public function retrieve(string $id): VerificationRequest
    {
        $data = $this->http->get('/api/v1/verification-requests/' . rawurlencode($id));
        return VerificationRequest::fromArray($data);
    }

    public function list(array $params = []): VerificationRequestListResponse
    {
        $data = $this->http->get('/api/v1/verification-requests', $params);
        return VerificationRequestListResponse::fromArray($data);
    }

    /** Get a verification request by its reference ID. */
    public function getByReference(string $referenceId): VerificationRequest
    {
        $data = $this->http->get('/api/v1/verification-requests/by-reference/' . rawurlencode($referenceId));
        return VerificationRequest::fromArray($data);
    }

    public function cancel(string $id): CancelRequestResponse
    {
        $data = $this->http->delete('/api/v1/verification-requests/' . rawurlencode($id));
        return CancelRequestResponse::fromArray($data);
    }

    public function waitForCompletion(string $id, float $interval = Polling::DEFAULT_INTERVAL, float $timeout = Polling::DEFAULT_TIMEOUT): VerificationRequest
    {
        $data = Polling::waitUntilComplete(
            fn () => $this->http->get('/api/v1/verification-requests/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Verification request {$id}",
            $interval,
            $timeout,
        );
        return VerificationRequest::fromArray($data);
    }
}

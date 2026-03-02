<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\VerificationRequest;

class UserRequests
{
    public function __construct(private readonly HttpClient $http) {}

    /** List my verification requests. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/verification-requests');
    }

    /** List incoming verification requests. */
    public function listIncoming(): array
    {
        return $this->http->get('/api/v1/me/verification-requests/incoming');
    }

    /** Create a verification request. */
    public function create(array $params): VerificationRequest
    {
        $data = $this->http->post('/api/v1/me/verification-requests', $params);
        return VerificationRequest::fromArray($data);
    }

    /** Claim assets from a verification request. */
    public function claim(string $requestId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/verification-requests/' . rawurlencode($requestId) . '/claim');
        return SuccessResponse::fromArray($data);
    }

    /** Cancel a verification request. */
    public function cancel(string $requestId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/verification-requests/' . rawurlencode($requestId));
        return SuccessResponse::fromArray($data);
    }

    /** Extend a verification request. */
    public function extend(string $requestId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/verification-requests/' . rawurlencode($requestId) . '/extend');
        return SuccessResponse::fromArray($data);
    }

    /** Share verification request via email. */
    public function shareEmail(string $requestId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/verification-requests/' . rawurlencode($requestId) . '/share-email');
        return SuccessResponse::fromArray($data);
    }
}

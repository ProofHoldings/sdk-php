<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\UserEmail;

class Emails
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all emails for the authenticated user. */
    public function list(): array
    {
        return $this->http->get('/api/v1/me/emails');
    }

    /** Remove an email by ID. */
    public function remove(string $emailId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/emails/' . rawurlencode($emailId));
        return SuccessResponse::fromArray($data);
    }

    /** Set an email as the primary email. */
    public function setPrimary(string $emailId): SuccessResponse
    {
        $data = $this->http->put('/api/v1/me/emails/' . rawurlencode($emailId) . '/primary');
        return SuccessResponse::fromArray($data);
    }

    /** Start adding a new email. */
    public function startAdd(array $params): array
    {
        return $this->http->post('/api/v1/me/emails/add', $params);
    }

    /** Get the status of an email add session. */
    public function getAddStatus(string $sessionId): array
    {
        return $this->http->get('/api/v1/me/emails/add/' . rawurlencode($sessionId));
    }

    /** Verify an email using an OTP code. */
    public function verifyOtp(string $sessionId, array $params): UserEmail
    {
        $data = $this->http->post('/api/v1/me/emails/add/' . rawurlencode($sessionId) . '/verify', $params);
        return UserEmail::fromArray($data);
    }

    /** Resend the email OTP. */
    public function resendOtp(string $sessionId): SuccessResponse
    {
        $data = $this->http->post('/api/v1/me/emails/add/' . rawurlencode($sessionId) . '/resend');
        return SuccessResponse::fromArray($data);
    }
}

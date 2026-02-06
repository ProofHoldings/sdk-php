<?php

declare(strict_types=1);

namespace ProofHoldings;

use ProofHoldings\Resources\Verifications;
use ProofHoldings\Resources\VerificationRequests;
use ProofHoldings\Resources\Proofs;
use ProofHoldings\Resources\Sessions;
use ProofHoldings\Resources\WebhookDeliveries;

class ProofHoldings
{
    private const string DEFAULT_BASE_URL = 'https://api.proof.holdings';
    private const float DEFAULT_TIMEOUT = 30.0;
    private const int DEFAULT_MAX_RETRIES = 2;

    public readonly Verifications $verifications;
    public readonly VerificationRequests $verificationRequests;
    public readonly Proofs $proofs;
    public readonly Sessions $sessions;
    public readonly WebhookDeliveries $webhookDeliveries;

    public function __construct(
        string $apiKey,
        string $baseUrl = self::DEFAULT_BASE_URL,
        float $timeout = self::DEFAULT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('api_key is required');
        }

        $http = new HttpClient($apiKey, $baseUrl, $timeout, $maxRetries);

        $this->verifications = new Verifications($http);
        $this->verificationRequests = new VerificationRequests($http);
        $this->proofs = new Proofs($http);
        $this->sessions = new Sessions($http);
        $this->webhookDeliveries = new WebhookDeliveries($http);
    }
}

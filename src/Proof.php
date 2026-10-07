<?php

declare(strict_types=1);

namespace ProofHoldings;

use ProofHoldings\Jwks\KeySetCache;
use ProofHoldings\Resources\Authorizations;
use ProofHoldings\Resources\AuthSessions;
use ProofHoldings\Resources\Circles;
use ProofHoldings\Resources\Confirmations;
use ProofHoldings\Resources\Delegations;
use ProofHoldings\Resources\Hitl;
use ProofHoldings\Resources\HitlChatId;
use ProofHoldings\Resources\HitlKeys;
use ProofHoldings\Resources\Me;
use ProofHoldings\Resources\Profiles;
use ProofHoldings\Resources\ProofMe;
use ProofHoldings\Resources\Proofs;
use ProofHoldings\Resources\PublicProfiles;
use ProofHoldings\Resources\Sessions;
use ProofHoldings\Resources\Templates;
use ProofHoldings\Resources\VerificationRequests;
use ProofHoldings\Resources\Verifications;
use ProofHoldings\Resources\WebhookDeliveries;

class Proof
{
    private const string DEFAULT_BASE_URL = 'https://api.proof.holdings';
    private const float DEFAULT_TIMEOUT = 30.0;
    private const int DEFAULT_MAX_RETRIES = 2;

    public readonly Verifications $verifications;
    public readonly VerificationRequests $verificationRequests;
    public readonly Proofs $proofs;
    public readonly Sessions $sessions;
    public readonly WebhookDeliveries $webhookDeliveries;
    public readonly Authorizations $authorizations;
    public readonly Confirmations $confirmations;
    public readonly Hitl $hitl;
    public readonly HitlKeys $hitlKeys;
    public readonly HitlChatId $hitlChatId;
    public readonly AuthSessions $authSessions;
    public readonly Me $me;
    public readonly Templates $templates;
    public readonly Profiles $profiles;
    public readonly PublicProfiles $publicProfiles;
    public readonly ProofMe $proofMe;
    public readonly Circles $circles;
    public readonly Delegations $delegations;

    public function __construct(
        string $apiKey,
        string $baseUrl = self::DEFAULT_BASE_URL,
        float $timeout = self::DEFAULT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
        int|float $maxJwksAge = KeySetCache::DEFAULT_MAX_JWKS_AGE_SECONDS,
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('api_key is required. Pass your key as: new Proof("pk_live_...")');
        }

        $http = new HttpClient($apiKey, $baseUrl, $timeout, $maxRetries);

        $this->verifications = new Verifications($http);
        $this->verificationRequests = new VerificationRequests($http);
        $this->proofs = new Proofs($http, $maxJwksAge);
        $this->sessions = new Sessions($http);
        $this->webhookDeliveries = new WebhookDeliveries($http);
        $this->authorizations = new Authorizations($http);
        $this->confirmations = new Confirmations($http);
        $this->hitl = new Hitl($http);
        $this->hitlKeys = new HitlKeys($http);
        $this->hitlChatId = new HitlChatId($http);
        $this->authSessions = new AuthSessions($http);
        $this->me = new Me($http);
        $this->templates = new Templates($http);
        $this->profiles = new Profiles($http);
        $this->publicProfiles = new PublicProfiles($http);
        $this->proofMe = new ProofMe($http);
        $this->circles = new Circles($http);
        $this->delegations = new Delegations($http);
    }
}

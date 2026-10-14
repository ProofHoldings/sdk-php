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

    /**
     * With a key it is used as given. Without one — `new Proof()` — it is resolved the way
     * `npx @proof-holdings/mcp-server login` expects: a non-empty `PROOF_API_KEY`, else the entry
     * saved for this client's base URL in `~/.proof-holdings/credentials`. An explicit empty key
     * is an error and never reads either. The base URL is the explicit argument, else a non-empty
     * `PROOF_BASE_URL`, else the default.
     */
    public function __construct(
        ?string $apiKey = null,
        ?string $baseUrl = null,
        float $timeout = self::DEFAULT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
        int|float $maxJwksAge = KeySetCache::DEFAULT_MAX_JWKS_AGE_SECONDS,
    ) {
        $resolvedBaseUrl = Credentials::resolveBaseUrl($baseUrl);
        if ($apiKey !== null && $apiKey === '') {
            throw new \InvalidArgumentException('api_key is required. Pass your key as: new Proof("pk_live_...")');
        }
        $resolved = Credentials::resolveApiKey($apiKey, $resolvedBaseUrl);
        if ($resolved->apiKey === null || $resolved->apiKey === '') {
            throw new \InvalidArgumentException(Credentials::missingKeyMessage($resolvedBaseUrl, $resolved->notice));
        }

        $http = new HttpClient($resolved->apiKey, $resolvedBaseUrl, $timeout, $maxRetries);

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

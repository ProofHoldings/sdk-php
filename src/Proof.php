<?php

declare(strict_types=1);

namespace ProofHoldings;

use ProofHoldings\Resources\Verifications;
use ProofHoldings\Resources\VerificationRequests;
use ProofHoldings\Resources\Proofs;
use ProofHoldings\Resources\Sessions;
use ProofHoldings\Resources\WebhookDeliveries;
use ProofHoldings\Resources\Templates;
use ProofHoldings\Resources\Profiles;
use ProofHoldings\Resources\Projects;
use ProofHoldings\Resources\Billing;
use ProofHoldings\Resources\Phones;
use ProofHoldings\Resources\Emails;
use ProofHoldings\Resources\Assets;
use ProofHoldings\Resources\Auth;
use ProofHoldings\Resources\Settings;
use ProofHoldings\Resources\ApiKeys;
use ProofHoldings\Resources\Account;
use ProofHoldings\Resources\TwoFA;
use ProofHoldings\Resources\DnsCredentials;
use ProofHoldings\Resources\Domains;
use ProofHoldings\Resources\UserRequests;
use ProofHoldings\Resources\UserDomainVerify;
use ProofHoldings\Resources\PublicProfiles;

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
    public readonly Templates $templates;
    public readonly Profiles $profiles;
    public readonly Projects $projects;
    public readonly Billing $billing;
    public readonly Phones $phones;
    public readonly Emails $emails;
    public readonly Assets $assets;
    public readonly Auth $auth;
    public readonly Settings $settings;
    public readonly ApiKeys $apiKeys;
    public readonly Account $account;
    public readonly TwoFA $twoFA;
    public readonly DnsCredentials $dnsCredentials;
    public readonly Domains $domains;
    public readonly UserRequests $userRequests;
    public readonly UserDomainVerify $userDomainVerify;
    public readonly PublicProfiles $publicProfiles;

    public function __construct(
        string $apiKey,
        string $baseUrl = self::DEFAULT_BASE_URL,
        float $timeout = self::DEFAULT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('api_key is required. Pass your key as: new Proof("pk_live_...")');
        }

        $http = new HttpClient($apiKey, $baseUrl, $timeout, $maxRetries);

        $this->verifications = new Verifications($http);
        $this->verificationRequests = new VerificationRequests($http);
        $this->proofs = new Proofs($http);
        $this->sessions = new Sessions($http);
        $this->webhookDeliveries = new WebhookDeliveries($http);
        $this->templates = new Templates($http);
        $this->profiles = new Profiles($http);
        $this->projects = new Projects($http);
        $this->billing = new Billing($http);
        $this->phones = new Phones($http);
        $this->emails = new Emails($http);
        $this->assets = new Assets($http);
        $this->auth = new Auth($http);
        $this->settings = new Settings($http);
        $this->apiKeys = new ApiKeys($http);
        $this->account = new Account($http);
        $this->twoFA = new TwoFA($http);
        $this->dnsCredentials = new DnsCredentials($http);
        $this->domains = new Domains($http);
        $this->userRequests = new UserRequests($http);
        $this->userDomainVerify = new UserDomainVerify($http);
        $this->publicProfiles = new PublicProfiles($http);
    }
}

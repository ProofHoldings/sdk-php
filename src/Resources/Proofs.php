<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\ProofStatusResponse;
use ProofHoldings\Types\RevocationList;
use ProofHoldings\Types\RevokeProofResponse;
use ProofHoldings\Types\ValidateProofResponse;

class Proofs
{
    private string $jwksUrl;
    /** @var array<string, \Firebase\JWT\Key>|null */
    private ?array $cachedKeySet = null;

    public function __construct(private readonly HttpClient $http)
    {
        $this->jwksUrl = $http->baseUrl . '/.well-known/jwks.json';
    }

    /** Validate a proof token online (checks revocation status). Public endpoint — no API key required. */
    public function validate(string $proofToken): ValidateProofResponse
    {
        $body = ['proof_token' => $proofToken];
        $data = $this->http->post('/api/v1/proofs/validate', $body);
        return ValidateProofResponse::fromArray($data);
    }

    public function revoke(string $id, ?string $reason = null): RevokeProofResponse
    {
        $body = [];
        if ($reason !== null) {
            $body['reason'] = $reason;
        }
        $data = $this->http->post('/api/v1/proofs/' . rawurlencode($id) . '/revoke', $body);
        return RevokeProofResponse::fromArray($data);
    }

    /** Get the status of a proof by verification ID. */
    public function status(string $id): ProofStatusResponse
    {
        $data = $this->http->get('/api/v1/proofs/' . rawurlencode($id) . '/status');
        return ProofStatusResponse::fromArray($data);
    }

    public function listRevoked(): RevocationList
    {
        $data = $this->http->get('/api/v1/proofs/revoked');
        return RevocationList::fromArray($data);
    }

    /**
     * Verify a proof token offline using JWKS public keys.
     *
     * Requires the firebase/php-jwt package:
     *   composer require firebase/php-jwt
     */
    public function verifyOffline(string $token): array
    {
        if (!class_exists(\Firebase\JWT\JWT::class)) {
            throw new \RuntimeException(
                'Offline verification requires firebase/php-jwt. Install with: composer require firebase/php-jwt'
            );
        }

        try {
            if ($this->cachedKeySet === null) {
                $jwksJson = file_get_contents($this->jwksUrl);
                if ($jwksJson === false) {
                    return ['valid' => false, 'error' => 'Failed to fetch JWKS'];
                }
                $jwks = json_decode($jwksJson, true);
                $this->cachedKeySet = \Firebase\JWT\JWK::parseKeySet($jwks);
            }

            $payload = \Firebase\JWT\JWT::decode($token, $this->cachedKeySet);
            $payloadArray = (array) $payload;

            return [
                'valid' => true,
                'payload' => [
                    'iss' => $payloadArray['iss'] ?? null,
                    'sub' => $payloadArray['sub'] ?? null,
                    'iat' => $payloadArray['iat'] ?? null,
                    'exp' => $payloadArray['exp'] ?? null,
                    'type' => $payloadArray['type'] ?? null,
                    'channel' => $payloadArray['channel'] ?? null,
                    'identifier_hash' => $payloadArray['identifier_hash'] ?? null,
                    'verified_at' => $payloadArray['verified_at'] ?? null,
                    'user_id' => $payloadArray['user_id'] ?? null,
                ],
            ];
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Force-refresh the cached JWKS (e.g. after key rotation).
     */
    public function refreshJwks(): void
    {
        $this->cachedKeySet = null;
    }
}

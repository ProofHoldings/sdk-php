<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

class Proofs
{
    private string $jwksUrl;

    public function __construct(private readonly HttpClient $http)
    {
        $this->jwksUrl = $http->baseUrl . '/.well-known/jwks.json';
    }

    public function validate(string $proofToken, ?string $identifier = null): array
    {
        $body = ['proof_token' => $proofToken];
        if ($identifier !== null) {
            $body['identifier'] = $identifier;
        }
        return $this->http->post('/api/v1/proofs/validate', $body);
    }

    public function revoke(string $id, ?string $reason = null): array
    {
        $body = [];
        if ($reason !== null) {
            $body['reason'] = $reason;
        }
        return $this->http->post('/api/v1/proofs/' . rawurlencode($id) . '/revoke', $body);
    }

    public function listRevoked(): array
    {
        return $this->http->get('/api/v1/proofs/revoked');
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
            // Fetch JWKS
            $jwksJson = file_get_contents($this->jwksUrl);
            if ($jwksJson === false) {
                return ['valid' => false, 'error' => 'Failed to fetch JWKS'];
            }

            $jwks = json_decode($jwksJson, true);
            $keySet = \Firebase\JWT\JWK::parseKeySet($jwks);

            $payload = \Firebase\JWT\JWT::decode($token, $keySet);
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
}

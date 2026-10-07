<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use ProofHoldings\Proof;
use ProofHoldings\Version;

class ClientIntegrationTest extends IntegrationTestCase
{
    public function testExposesEveryResource(): void
    {
        $proof = $this->makeClient();
        $this->assertInstanceOf(\ProofHoldings\Resources\Verifications::class, $proof->verifications);
        $this->assertInstanceOf(\ProofHoldings\Resources\VerificationRequests::class, $proof->verificationRequests);
        $this->assertInstanceOf(\ProofHoldings\Resources\Proofs::class, $proof->proofs);
        $this->assertInstanceOf(\ProofHoldings\Resources\Sessions::class, $proof->sessions);
        $this->assertInstanceOf(\ProofHoldings\Resources\WebhookDeliveries::class, $proof->webhookDeliveries);
        $this->assertInstanceOf(\ProofHoldings\Resources\Authorizations::class, $proof->authorizations);
        $this->assertInstanceOf(\ProofHoldings\Resources\Confirmations::class, $proof->confirmations);
        $this->assertInstanceOf(\ProofHoldings\Resources\Me::class, $proof->me);
    }

    public function testEmptyApiKeyRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Proof('');
    }

    public function testCustomBaseUrlAccepted(): void
    {
        $proof = new Proof('pk_test_xyz', 'https://example.test', 1.0, 5);
        $this->assertInstanceOf(Proof::class, $proof);
    }

    public function testLiveHealthReachable(): void
    {
        if (!self::isLiveMode()) {
            $this->markTestSkipped('live mode not configured');
        }

        $base = rtrim((string) getenv('PROOF_BASE_URL'), '/');
        // /health has no auth but staging fronts the origin with Cloudflare
        // Access — apply the service-token headers so the request reaches
        // the real backend instead of being intercepted.
        //
        // Send the SDK's branded User-Agent. The staging origin's marketing
        // nginx (frontend.conf) returns 444 for library-default UAs
        // (``python-httpx/*``, ``Go-http-client/*``, etc.) which surfaces as
        // CF 520. ``GuzzleHttp/7`` is not on that list today, but fixing this
        // defensively matches the Python/Go harnesses and prevents silent
        // regression if the list is ever widened.
        $client = new \GuzzleHttp\Client([
            'timeout' => 10.0,
            'headers' => array_merge(
                ['User-Agent' => 'proof-sdk-php/' . Version::VERSION],
                self::cfAccessHeaders(),
            ),
        ]);
        $response = $client->get("{$base}/health", ['http_errors' => false]);
        $this->assertSame(
            200,
            $response->getStatusCode(),
            'Expected 200 from /health; got ' . $response->getStatusCode(),
        );
    }

    public function testLiveBadApiKeyIsRejected(): void
    {
        if (!self::isLiveMode()) {
            $this->markTestSkipped('live mode only');
        }

        // Build a Proof client with a bogus key, then swap its Guzzle for one
        // carrying CF Access headers so the request actually reaches the API
        // origin and the 401 is a real-auth failure, not a Cloudflare 403.
        $base = (string) getenv('PROOF_BASE_URL');
        $proof = new Proof('pk_test_this_key_does_not_exist', $base, 15.0, 0);

        $access = self::cfAccessHeaders();
        if (!empty($access)) {
            $guzzle = new \GuzzleHttp\Client([
                'base_uri' => rtrim($base, '/'),
                'timeout' => 15.0,
                'headers' => array_merge(
                    [
                        'Authorization' => 'Bearer pk_test_this_key_does_not_exist',
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
                    ],
                    $access,
                ),
            ]);
            $resourceHttp = $this->readPrivate($proof->verifications, 'http');
            $this->writePrivate($resourceHttp, 'client', $guzzle);
        }

        $this->expectException(\ProofHoldings\Exceptions\AuthenticationException::class);
        $proof->verifications->list();
    }
}

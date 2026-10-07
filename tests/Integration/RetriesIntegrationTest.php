<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ProofHoldings\Proof;
use ProofHoldings\Version;

class RetriesIntegrationTest extends IntegrationTestCase
{
    public function testRetriesOnceOn503ThenSucceeds(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped('mock-only — exercises HttpClient retry path');
        }

        $mock = new MockHandler([
            $this->jsonResponse(503, ['error' => ['code' => 'service_unavailable', 'message' => 'retry']]),
            $this->jsonResponse(200, [
                'data' => [],
                'pagination' => ['page' => 1, 'limit' => 20, 'total' => 0, 'pages' => 0],
            ]),
        ]);

        // Build a Proof client with maxRetries=1, then swap its Guzzle client
        // to the mock handler so we can count attempts via MockHandler::count().
        $proof = new Proof('pk_test_retry', 'https://mock.proof.test', 5.0, 1);
        $stack = HandlerStack::create($mock);
        $guzzle = new Client([
            'base_uri' => 'https://mock.proof.test',
            'timeout' => 5.0,
            'handler' => $stack,
            'headers' => [
                'Authorization' => 'Bearer pk_test_retry',
                'Content-Type' => 'application/json',
                'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
            ],
        ]);
        $resourceHttp = $this->readPrivate($proof->verifications, 'http');
        $this->writePrivate($resourceHttp, 'client', $guzzle);

        $page = $proof->verifications->list();
        $this->assertSame([], $page['data']);
        // MockHandler exhausts its queued responses — 0 remaining means both attempts were served.
        $this->assertSame(0, $mock->count());
    }
}

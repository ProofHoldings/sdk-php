<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\Proof;

class ClientTest extends TestCase
{
    public function testEmptyKeyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Proof('');
    }

    public function testValidKeyCreatesClient(): void
    {
        $client = new Proof('pk_test_123');
        $this->assertInstanceOf(Proof::class, $client);
    }

    public function testResourcesAreInitialized(): void
    {
        $client = new Proof('pk_test_123');
        $this->assertInstanceOf(\ProofHoldings\Resources\Verifications::class, $client->verifications);
        $this->assertInstanceOf(\ProofHoldings\Resources\VerificationRequests::class, $client->verificationRequests);
        $this->assertInstanceOf(\ProofHoldings\Resources\Proofs::class, $client->proofs);
        $this->assertInstanceOf(\ProofHoldings\Resources\Sessions::class, $client->sessions);
        $this->assertInstanceOf(\ProofHoldings\Resources\WebhookDeliveries::class, $client->webhookDeliveries);
    }

    public function testCustomOptions(): void
    {
        $client = new Proof('pk_test_123', baseUrl: 'https://custom.api.com', timeout: 60.0, maxRetries: 5);
        $this->assertInstanceOf(Proof::class, $client);
    }
}

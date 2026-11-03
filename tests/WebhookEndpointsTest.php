<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Proof;
use ProofHoldings\Resources\Profiles;
use ProofHoldings\Resources\WebhookEndpoints;

class WebhookEndpointsTest extends TestCase
{
    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    public function testListGetsCollection(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-endpoints')
            ->willReturn(['data' => [['id' => 'whe_1']]]);

        $result = (new WebhookEndpoints($http))->list();
        $this->assertSame('whe_1', $result['data'][0]['id']);
    }

    public function testCreatePostsBodyAndReturnsSecret(): void
    {
        $params = ['url' => 'https://example.com/hook', 'scope' => 'all', 'events' => ['verification.completed']];
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/webhook-endpoints', $params)
            ->willReturn(['id' => 'whe_1', 'secret' => 'whsec_x']);

        $result = (new WebhookEndpoints($http))->create($params);
        $this->assertSame('whsec_x', $result['secret']);
    }

    public function testGetById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-endpoints/whe_1')
            ->willReturn(['id' => 'whe_1']);

        (new WebhookEndpoints($http))->get('whe_1');
    }

    public function testGetEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-endpoints/whe%2Fx%26y')
            ->willReturn([]);

        (new WebhookEndpoints($http))->get('whe/x&y');
    }

    public function testUpdatePatchesBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/webhook-endpoints/whe_1', ['status' => 'disabled'])
            ->willReturn(['id' => 'whe_1', 'status' => 'disabled']);

        $result = (new WebhookEndpoints($http))->update('whe_1', ['status' => 'disabled']);
        $this->assertSame('disabled', $result['status']);
    }

    public function testDeleteSendsDeleteWithoutBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/webhook-endpoints/whe_1')
            ->willReturn([]);

        (new WebhookEndpoints($http))->delete('whe_1');
    }

    public function testRotateSecretPostsWithoutBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/webhook-endpoints/whe_1/rotate-secret')
            ->willReturn(['id' => 'whe_1', 'secret' => 'whsec_new']);

        $result = (new WebhookEndpoints($http))->rotateSecret('whe_1');
        $this->assertSame('whsec_new', $result['secret']);
    }

    public function testProfilesRotateCallbackSecret(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/profiles/profiles/prof%2F1/callback-secret/rotate')
            ->willReturn(['profile_id' => 'prof/1', 'secret' => 'cbsec_x']);

        $result = (new Profiles($http))->rotateCallbackSecret('prof/1');
        $this->assertSame('cbsec_x', $result['secret']);
    }

    public function testClientWiresWebhookEndpoints(): void
    {
        $proof = new Proof('pk_test_x', 'https://api.example.test');
        $this->assertInstanceOf(WebhookEndpoints::class, $proof->webhookEndpoints);
    }
}

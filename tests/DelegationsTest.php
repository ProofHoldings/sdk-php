<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Delegations;

class DelegationsTest extends TestCase
{
    private const string PH_DLG = 'ph_dlg_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private const string PH_CTL = 'ph_ctl_bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb';

    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    public function testCreatePostsTypedDelegateBody(): void
    {
        $params = [
            'control_proof' => self::PH_CTL,
            'delegate' => ['type' => 'purl', 'value' => 'pkg:npm/@postmark/mcp'],
            'scope' => ['email:send'],
        ];
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/delegations', $params)
            ->willReturn(['id' => '507f1f77bcf86cd799439011', 'token' => 'eyJ.token.sig']);

        $d = new Delegations($http);
        $result = $d->create($params);
        $this->assertSame('eyJ.token.sig', $result['token']);
    }

    public function testRetrieveByHandle(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/delegations/' . self::PH_DLG)
            ->willReturn(['token' => 'eyJ.token.sig', 'effective_status' => 'active', 'is_valid' => true]);

        $d = new Delegations($http);
        $result = $d->retrieve(self::PH_DLG);
        $this->assertTrue($result['is_valid']);
    }

    public function testRetrieveEncodesId(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/delegations/dlg%2Fspecial%26id')
            ->willReturn([]);

        $d = new Delegations($http);
        $d->retrieve('dlg/special&id');
    }

    public function testListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/delegations', ['status' => 'revoked', 'page' => 2])
            ->willReturn(['data' => [], 'pagination' => ['page' => 2, 'limit' => 20, 'total' => 0]]);

        $d = new Delegations($http);
        $d->list(['status' => 'revoked', 'page' => 2]);
    }

    public function testRevokeWithoutReasonSendsNoBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/delegations/' . self::PH_DLG . '/revoke', null)
            ->willReturn(['status' => 'revoked']);

        $d = new Delegations($http);
        $d->revoke(self::PH_DLG);
    }

    public function testRevokePostsReason(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/delegations/' . self::PH_DLG . '/revoke', ['reason' => 'key_compromise'])
            ->willReturn(['status' => 'revoked']);

        $d = new Delegations($http);
        $result = $d->revoke(self::PH_DLG, ['reason' => 'key_compromise']);
        $this->assertSame('revoked', $result['status']);
    }
}

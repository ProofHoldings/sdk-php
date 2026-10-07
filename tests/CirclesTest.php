<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Circles;

class CirclesTest extends TestCase
{
    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    public function testCreatePostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/circles', ['name' => 'Family'])
            ->willReturn(['id' => 'circ_1', 'name' => 'Family']);

        $c = new Circles($http);
        $result = $c->create(['name' => 'Family']);
        $this->assertSame('circ_1', $result['id']);
    }

    public function testListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/circles', ['status' => 'archived'])
            ->willReturn(['data' => []]);

        $c = new Circles($http);
        $c->list(['status' => 'archived']);
    }

    public function testGetById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/circles/circ_1')
            ->willReturn(['id' => 'circ_1']);

        $c = new Circles($http);
        $c->get('circ_1');
    }

    public function testGetEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/circles/circ%2Fx%26y')
            ->willReturn([]);

        $c = new Circles($http);
        $c->get('circ/x&y');
    }

    public function testUpdatePatchesEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/circles/circ_1', ['name' => 'Close Family'])
            ->willReturn(['id' => 'circ_1', 'name' => 'Close Family']);

        $c = new Circles($http);
        $c->update('circ_1', ['name' => 'Close Family']);
    }

    public function testArchiveDeletesEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/circles/circ_1')
            ->willReturn([]);

        $c = new Circles($http);
        $c->archive('circ_1');
    }

    public function testAddMemberPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/circles/circ_1/members', ['name' => 'Mum'])
            ->willReturn(['id' => 'mem_1']);

        $c = new Circles($http);
        $c->addMember('circ_1', ['name' => 'Mum']);
    }

    public function testListMembersGetsEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/circles/circ_1/members')
            ->willReturn(['members' => []]);

        $c = new Circles($http);
        $c->listMembers('circ_1');
    }

    public function testInviteMemberPostsWithMemberId(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/circles/circ_1/members/invite', ['member_id' => 'mem_1'])
            ->willReturn(['token' => 'tok_1']);

        $c = new Circles($http);
        $c->inviteMember('circ_1', 'mem_1');
    }

    public function testTriggerDrillPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/circles/circ_1/members/mem_1/drill')
            ->willReturn(['id' => 'auth_1', 'status' => 'pending']);

        $c = new Circles($http);
        $c->triggerDrill('circ_1', 'mem_1');
    }

    public function testRemoveMemberDeletesEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/circles/circ_1/members/mem_1')
            ->willReturn([]);

        $c = new Circles($http);
        $c->removeMember('circ_1', 'mem_1');
    }

    public function testAddMemberChannelPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/circles/circ_1/members/mem_1/channels', ['channel' => 'whatsapp', 'identifier' => '+15551112222'])
            ->willReturn(['id' => 'chan_1', 'status' => 'declared']);

        $c = new Circles($http);
        $c->addMemberChannel('circ_1', 'mem_1', ['channel' => 'whatsapp', 'identifier' => '+15551112222']);
    }

    public function testListMemberChannelsGetsEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/circles/circ_1/members/mem_1/channels')
            ->willReturn(['channels' => []]);

        $c = new Circles($http);
        $c->listMemberChannels('circ_1', 'mem_1');
    }

    public function testRemoveMemberChannelDeletesEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/circles/circ_1/members/mem_1/channels/chan_1')
            ->willReturn([]);

        $c = new Circles($http);
        $c->removeMemberChannel('circ_1', 'mem_1', 'chan_1');
    }
}

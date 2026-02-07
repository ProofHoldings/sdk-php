<?php

declare(strict_types=1);

namespace Proof\Tests;

use PHPUnit\Framework\TestCase;
use Proof\HttpClient;
use Proof\Resources\Verifications;
use Proof\Resources\Sessions;
use Proof\Resources\VerificationRequests;
use Proof\Resources\WebhookDeliveries;

class ResourcesTest extends TestCase
{
    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    // --- Verifications ---

    public function testVerificationsCreatePostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications', ['type' => 'phone', 'channel' => 'whatsapp'])
            ->willReturn(['id' => 'ver_123', 'status' => 'pending']);

        $v = new Verifications($http);
        $result = $v->create(['type' => 'phone', 'channel' => 'whatsapp']);
        $this->assertSame('ver_123', $result['id']);
    }

    public function testVerificationsRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/ver_123')
            ->willReturn(['id' => 'ver_123']);

        $v = new Verifications($http);
        $v->retrieve('ver_123');
    }

    public function testVerificationsRetrieveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/ver%2Fspecial%26id')
            ->willReturn([]);

        $v = new Verifications($http);
        $v->retrieve('ver/special&id');
    }

    public function testVerificationsListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications', ['status' => 'verified', 'limit' => 10])
            ->willReturn(['data' => []]);

        $v = new Verifications($http);
        $v->list(['status' => 'verified', 'limit' => 10]);
    }

    public function testVerificationsVerifyPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications/ver_123/verify')
            ->willReturn(['status' => 'verified']);

        $v = new Verifications($http);
        $v->verify('ver_123');
    }

    public function testVerificationsSubmitPostsCode(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications/ver_123/submit', ['code' => 'ABC123'])
            ->willReturn(['status' => 'verified']);

        $v = new Verifications($http);
        $v->submit('ver_123', 'ABC123');
    }

    // --- Sessions ---

    public function testSessionsCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/sessions', ['channel' => 'telegram'])
            ->willReturn(['id' => 'ses_123']);

        $s = new Sessions($http);
        $s->create(['channel' => 'telegram']);
    }

    public function testSessionsRetrieveGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/sessions/ses_123')
            ->willReturn(['id' => 'ses_123']);

        $s = new Sessions($http);
        $s->retrieve('ses_123');
    }

    // --- VerificationRequests ---

    public function testVerificationRequestsCreatePostsAssets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verification-requests', [
                'assets' => [['type' => 'phone', 'required' => true]],
                'reference_id' => 'user_123',
            ])
            ->willReturn(['id' => 'vr_123']);

        $vr = new VerificationRequests($http);
        $vr->create(['assets' => [['type' => 'phone', 'required' => true]], 'reference_id' => 'user_123']);
    }

    public function testVerificationRequestsCancelDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/verification-requests/vr_123')
            ->willReturn(['status' => 'cancelled']);

        $vr = new VerificationRequests($http);
        $vr->cancel('vr_123');
    }

    public function testVerificationRequestsListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verification-requests', ['status' => 'pending', 'limit' => 5])
            ->willReturn(['data' => []]);

        $vr = new VerificationRequests($http);
        $vr->list(['status' => 'pending', 'limit' => 5]);
    }

    // --- WebhookDeliveries ---

    public function testWebhookDeliveriesListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-deliveries', ['status' => 'failed'])
            ->willReturn(['deliveries' => []]);

        $wd = new WebhookDeliveries($http);
        $wd->list(['status' => 'failed']);
    }

    public function testWebhookDeliveriesRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-deliveries/del_123')
            ->willReturn(['id' => 'del_123']);

        $wd = new WebhookDeliveries($http);
        $wd->retrieve('del_123');
    }

    public function testWebhookDeliveriesRetryPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/webhook-deliveries/del_123/retry')
            ->willReturn(['success' => true]);

        $wd = new WebhookDeliveries($http);
        $wd->retry('del_123');
    }
}

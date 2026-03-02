<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Verifications;
use ProofHoldings\Resources\Sessions;
use ProofHoldings\Resources\VerificationRequests;
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
use ProofHoldings\Resources\Proofs;
use ProofHoldings\Types\APIKeyResponse;
use ProofHoldings\Types\AuthUser;
use ProofHoldings\Types\CancelRequestResponse;
use ProofHoldings\Types\CheckoutResponse;
use ProofHoldings\Types\DNSProviderCredentials;
use ProofHoldings\Types\Domain;
use ProofHoldings\Types\ListSessionsResponse;
use ProofHoldings\Types\PortalResponse;
use ProofHoldings\Types\ProofStatusResponse;
use ProofHoldings\Types\PublicProfile;
use ProofHoldings\Types\Project;
use ProofHoldings\Types\ResendVerificationResponse;
use ProofHoldings\Types\RetryDeliveryResponse;
use ProofHoldings\Types\RevocationList;
use ProofHoldings\Types\RevokeProofResponse;
use ProofHoldings\Types\Session;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\Template;
use ProofHoldings\Types\TestVerifyResponse;
use ProofHoldings\Types\UserAsset;
use ProofHoldings\Types\UserEmail;
use ProofHoldings\Types\UserPhone;
use ProofHoldings\Types\ValidateProofResponse;
use ProofHoldings\Types\Verification;
use ProofHoldings\Types\VerificationListResponse;
use ProofHoldings\Types\VerificationRequest;
use ProofHoldings\Types\VerificationRequestListResponse;
use ProofHoldings\Types\VerifiedUserDetail;
use ProofHoldings\Types\VerifiedUserListResponse;
use ProofHoldings\Types\DomainVerificationResponse;
use ProofHoldings\Types\DomainCheckResponse;
use ProofHoldings\Types\WebhookDelivery;
use ProofHoldings\Types\WebhookDeliveryListResponse;
use ProofHoldings\Types\WebhookDeliveryStats;

class ResourcesTest extends TestCase
{
    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    private function setMockProperty(object $object, string $property, mixed $value): void
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setValue($object, $value);
    }

    // --- Helper data builders ---

    private function verificationData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'ver_123',
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => '+1234567890',
            'status' => 'pending',
            'attempts' => 0,
            'max_attempts' => 3,
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function sessionData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'ses_123',
            'status' => 'pending',
            'created_at' => '2025-01-01T00:00:00Z',
            'expires_at' => '2025-01-02T00:00:00Z',
        ], $overrides);
    }

    private function verificationRequestData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'vr_123',
            'status' => 'pending',
            'assets' => [['type' => 'phone', 'required' => true]],
            'action_type' => 'verification',
            'partial_ok' => false,
            'expires_at' => '2025-01-02T00:00:00Z',
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function webhookDeliveryData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'del_123',
            'event_type' => 'verification.completed',
            'url' => 'https://example.com/webhook',
            'status' => 'delivered',
            'attempts' => 1,
            'max_attempts' => 3,
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function templateData(array $overrides = []): array
    {
        return array_merge([
            'channel' => 'email',
            'message_type' => 'verification_request',
            'body' => 'Your code is {{code}}',
        ], $overrides);
    }

    private function publicProfileData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'prof_123',
            'is_public' => true,
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function projectData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'proj_123',
            'name' => 'My Project',
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function domainData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'dom_123',
            'domain' => 'example.com',
            'status' => 'verified',
            'created_at' => '2025-01-01T00:00:00Z',
            'updated_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function apiKeyData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'key_123',
            'name' => 'My Key',
            'key_prefix' => 'pk_test_',
            'environment' => 'test',
            'scopes' => ['*'],
            'created_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function userAssetData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'asset_123',
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => '+1234567890',
            'status' => 'active',
            'created_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    private function userEmailData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'em_123',
            'email' => 'test@example.com',
            'is_primary' => true,
            'created_at' => '2025-01-01T00:00:00Z',
        ], $overrides);
    }

    // --- Verifications ---

    public function testVerificationsCreatePostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications', ['type' => 'phone', 'channel' => 'whatsapp'])
            ->willReturn($this->verificationData(['channel' => 'whatsapp']));

        $v = new Verifications($http);
        $result = $v->create(['type' => 'phone', 'channel' => 'whatsapp']);
        $this->assertInstanceOf(Verification::class, $result);
        $this->assertSame('ver_123', $result->id);
    }

    public function testVerificationsRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/ver_123')
            ->willReturn($this->verificationData());

        $v = new Verifications($http);
        $result = $v->retrieve('ver_123');
        $this->assertInstanceOf(Verification::class, $result);
    }

    public function testVerificationsRetrieveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/ver%2Fspecial%26id')
            ->willReturn($this->verificationData(['id' => 'ver/special&id']));

        $v = new Verifications($http);
        $v->retrieve('ver/special&id');
    }

    public function testVerificationsListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications', ['status' => 'verified', 'limit' => 10])
            ->willReturn(['data' => [], 'pagination' => ['hasMore' => false]]);

        $v = new Verifications($http);
        $result = $v->list(['status' => 'verified', 'limit' => 10]);
        $this->assertInstanceOf(VerificationListResponse::class, $result);
    }

    public function testVerificationsVerifyPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications/ver_123/verify')
            ->willReturn($this->verificationData(['status' => 'verified']));

        $v = new Verifications($http);
        $result = $v->verify('ver_123');
        $this->assertInstanceOf(Verification::class, $result);
    }

    public function testVerificationsSubmitPostsCode(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications/ver_123/submit', ['code' => 'ABC123'])
            ->willReturn($this->verificationData(['status' => 'verified']));

        $v = new Verifications($http);
        $result = $v->submit('ver_123', 'ABC123');
        $this->assertInstanceOf(Verification::class, $result);
    }

    // --- Sessions ---

    public function testSessionsCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/sessions', ['channel' => 'telegram'])
            ->willReturn($this->sessionData());

        $s = new Sessions($http);
        $result = $s->create(['channel' => 'telegram']);
        $this->assertInstanceOf(Session::class, $result);
    }

    public function testSessionsRetrieveGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/sessions/ses_123')
            ->willReturn($this->sessionData());

        $s = new Sessions($http);
        $result = $s->retrieve('ses_123');
        $this->assertInstanceOf(Session::class, $result);
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
            ->willReturn($this->verificationRequestData(['reference_id' => 'user_123']));

        $vr = new VerificationRequests($http);
        $result = $vr->create(['assets' => [['type' => 'phone', 'required' => true]], 'reference_id' => 'user_123']);
        $this->assertInstanceOf(VerificationRequest::class, $result);
    }

    public function testVerificationRequestsCancelDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/verification-requests/vr_123')
            ->willReturn(['success' => true, 'status' => 'cancelled']);

        $vr = new VerificationRequests($http);
        $result = $vr->cancel('vr_123');
        $this->assertInstanceOf(CancelRequestResponse::class, $result);
    }

    public function testVerificationRequestsListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verification-requests', ['status' => 'pending', 'limit' => 5])
            ->willReturn(['data' => [], 'pagination' => ['hasMore' => false]]);

        $vr = new VerificationRequests($http);
        $result = $vr->list(['status' => 'pending', 'limit' => 5]);
        $this->assertInstanceOf(VerificationRequestListResponse::class, $result);
    }

    // --- WebhookDeliveries ---

    public function testWebhookDeliveriesListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-deliveries', ['status' => 'failed'])
            ->willReturn(['data' => [], 'pagination' => ['hasMore' => false]]);

        $wd = new WebhookDeliveries($http);
        $result = $wd->list(['status' => 'failed']);
        $this->assertInstanceOf(WebhookDeliveryListResponse::class, $result);
    }

    public function testWebhookDeliveriesRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/webhook-deliveries/del_123')
            ->willReturn($this->webhookDeliveryData());

        $wd = new WebhookDeliveries($http);
        $result = $wd->retrieve('del_123');
        $this->assertInstanceOf(WebhookDelivery::class, $result);
    }

    public function testWebhookDeliveriesRetryPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/webhook-deliveries/del_123/retry')
            ->willReturn(['success' => true, 'delivery' => $this->webhookDeliveryData()]);

        $wd = new WebhookDeliveries($http);
        $result = $wd->retry('del_123');
        $this->assertInstanceOf(RetryDeliveryResponse::class, $result);
    }

    // --- Templates ---

    public function testTemplatesListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates')
            ->willReturn(['custom_templates' => []]);

        $t = new Templates($http);
        $t->list();
    }

    public function testTemplatesGetDefaultsGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates/defaults')
            ->willReturn(['defaults' => []]);

        $t = new Templates($http);
        $t->getDefaults();
    }

    public function testTemplatesRetrieveGetsWithChannelAndType(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates/email/verification_request')
            ->willReturn($this->templateData());

        $t = new Templates($http);
        $result = $t->retrieve('email', 'verification_request');
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testTemplatesRetrieveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates/sms%2Fspecial/2fa%26request')
            ->willReturn($this->templateData(['channel' => 'sms/special', 'message_type' => '2fa&request']));

        $t = new Templates($http);
        $t->retrieve('sms/special', '2fa&request');
    }

    public function testTemplatesUpsertPuts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/templates/telegram/login_request', ['body' => 'Hello {{code}}'])
            ->willReturn($this->templateData(['channel' => 'telegram', 'message_type' => 'login_request', 'body' => 'Hello {{code}}']));

        $t = new Templates($http);
        $result = $t->upsert('telegram', 'login_request', ['body' => 'Hello {{code}}']);
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testTemplatesDeleteDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/templates/whatsapp/verification_success')
            ->willReturn(['success' => true]);

        $t = new Templates($http);
        $result = $t->delete('whatsapp', 'verification_success');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testTemplatesPreviewPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/templates/preview', [
                'channel' => 'email',
                'message_type' => 'verification_request',
                'body' => 'Test {{code}}',
            ])
            ->willReturn(['preview' => [], 'is_valid' => true]);

        $t = new Templates($http);
        $t->preview(['channel' => 'email', 'message_type' => 'verification_request', 'body' => 'Test {{code}}']);
    }

    public function testTemplatesRenderPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/templates/render', [
                'channel' => 'sms',
                'message_type' => '2fa_request',
                'variables' => ['code' => '123456'],
            ])
            ->willReturn(['rendered' => []]);

        $t = new Templates($http);
        $t->render(['channel' => 'sms', 'message_type' => '2fa_request', 'variables' => ['code' => '123456']]);
    }

    // --- Profiles ---

    public function testProfilesListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles')
            ->willReturn(['profiles' => []]);

        $p = new Profiles($http);
        $p->list();
    }

    public function testProfilesCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/profiles', ['display_name' => 'Test', 'is_public' => true])
            ->willReturn($this->publicProfileData(['display_name' => 'Test']));

        $p = new Profiles($http);
        $result = $p->create(['display_name' => 'Test', 'is_public' => true]);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testProfilesRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles/prof_123')
            ->willReturn($this->publicProfileData());

        $p = new Profiles($http);
        $result = $p->retrieve('prof_123');
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testProfilesRetrieveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles/prof%2Fspecial%26id')
            ->willReturn($this->publicProfileData(['id' => 'prof/special&id']));

        $p = new Profiles($http);
        $p->retrieve('prof/special&id');
    }

    public function testProfilesUpdatePatches(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/me/profiles/prof_123', ['display_name' => 'Updated'])
            ->willReturn($this->publicProfileData(['display_name' => 'Updated']));

        $p = new Profiles($http);
        $result = $p->update('prof_123', ['display_name' => 'Updated']);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testProfilesDeleteDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/profiles/prof_123')
            ->willReturn(['success' => true]);

        $p = new Profiles($http);
        $result = $p->delete('prof_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testProfilesSetPrimaryPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/profiles/prof_123/primary')
            ->willReturn(['success' => true]);

        $p = new Profiles($http);
        $result = $p->setPrimary('prof_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    // --- Projects ---

    public function testProjectsListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/projects')
            ->willReturn(['data' => []]);

        $p = new Projects($http);
        $p->list();
    }

    public function testProjectsCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/projects', ['name' => 'My Project'])
            ->willReturn($this->projectData());

        $p = new Projects($http);
        $result = $p->create(['name' => 'My Project']);
        $this->assertInstanceOf(Project::class, $result);
    }

    public function testProjectsRetrieveGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/projects/proj_123')
            ->willReturn($this->projectData());

        $p = new Projects($http);
        $result = $p->retrieve('proj_123');
        $this->assertInstanceOf(Project::class, $result);
    }

    public function testProjectsRetrieveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/projects/proj%2Fspecial%26id')
            ->willReturn($this->projectData(['id' => 'proj/special&id']));

        $p = new Projects($http);
        $p->retrieve('proj/special&id');
    }

    public function testProjectsUpdatePuts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/me/projects/proj_123', ['name' => 'Updated'])
            ->willReturn($this->projectData(['name' => 'Updated']));

        $p = new Projects($http);
        $result = $p->update('proj_123', ['name' => 'Updated']);
        $this->assertInstanceOf(Project::class, $result);
    }

    public function testProjectsDeleteDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/projects/proj_123')
            ->willReturn(['success' => true]);

        $p = new Projects($http);
        $result = $p->delete('proj_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testProjectsListTemplatesGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/projects/proj_123/templates')
            ->willReturn(['templates' => []]);

        $p = new Projects($http);
        $p->listTemplates('proj_123');
    }

    public function testProjectsUpdateTemplatePuts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with(
                '/api/v1/me/projects/proj_123/templates/email/verification_request',
                ['body' => 'Hello {{code}}'],
            )
            ->willReturn($this->templateData(['body' => 'Hello {{code}}']));

        $p = new Projects($http);
        $result = $p->updateTemplate('proj_123', 'email', 'verification_request', ['body' => 'Hello {{code}}']);
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testProjectsDeleteTemplateDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/projects/proj_123/templates/sms/2fa_request')
            ->willReturn(['success' => true]);

        $p = new Projects($http);
        $result = $p->deleteTemplate('proj_123', 'sms', '2fa_request');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testProjectsPreviewTemplatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/projects/proj_123/templates/preview', [
                'channel' => 'email',
                'message_type' => 'verification_request',
                'body' => 'Test {{code}}',
            ])
            ->willReturn(['preview' => []]);

        $p = new Projects($http);
        $p->previewTemplate('proj_123', [
            'channel' => 'email',
            'message_type' => 'verification_request',
            'body' => 'Test {{code}}',
        ]);
    }

    // --- Billing ---

    public function testBillingSubscriptionGetsEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/billing/subscription')
            ->willReturn(['plan' => 'free']);

        $b = new Billing($http);
        $b->subscription();
    }

    public function testBillingCheckoutPostsWithParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/billing/checkout', [
                'plan' => 'pro',
                'success_url' => 'https://proof.holdings/success',
                'cancel_url' => 'https://proof.holdings/cancel',
            ])
            ->willReturn(['url' => 'https://checkout.stripe.com/...']);

        $b = new Billing($http);
        $result = $b->checkout([
            'plan' => 'pro',
            'success_url' => 'https://proof.holdings/success',
            'cancel_url' => 'https://proof.holdings/cancel',
        ]);
        $this->assertInstanceOf(CheckoutResponse::class, $result);
    }

    public function testBillingPortalPostsWithReturnUrl(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/billing/portal', [
                'return_url' => 'https://proof.holdings/billing',
            ])
            ->willReturn(['url' => 'https://billing.stripe.com/...']);

        $b = new Billing($http);
        $result = $b->portal([
            'return_url' => 'https://proof.holdings/billing',
        ]);
        $this->assertInstanceOf(PortalResponse::class, $result);
    }

    // --- Phones ---

    public function testPhonesListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/phones')
            ->willReturn(['phones' => []]);

        $p = new Phones($http);
        $p->list();
    }

    public function testPhonesRemoveDeletesById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/phones/ph_123')
            ->willReturn(['success' => true]);

        $p = new Phones($http);
        $result = $p->remove('ph_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testPhonesRemoveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/phones/ph%2Fspecial%26id')
            ->willReturn(['success' => true]);

        $p = new Phones($http);
        $p->remove('ph/special&id');
    }

    public function testPhonesSetPrimaryPuts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/me/phones/ph_123/primary')
            ->willReturn(['success' => true]);

        $p = new Phones($http);
        $result = $p->setPrimary('ph_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testPhonesStartAddPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/phones/add', ['channel' => 'telegram'])
            ->willReturn(['session_id' => 'ses_123']);

        $p = new Phones($http);
        $p->startAdd(['channel' => 'telegram']);
    }

    public function testPhonesGetAddStatusGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/phones/add/ses_123')
            ->willReturn(['status' => 'pending']);

        $p = new Phones($http);
        $p->getAddStatus('ses_123');
    }

    // --- Emails ---

    public function testEmailsListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/emails')
            ->willReturn(['emails' => []]);

        $e = new Emails($http);
        $e->list();
    }

    public function testEmailsRemoveDeletesById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/emails/em_123')
            ->willReturn(['success' => true]);

        $e = new Emails($http);
        $result = $e->remove('em_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testEmailsRemoveEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/emails/em%2Fspecial%26id')
            ->willReturn(['success' => true]);

        $e = new Emails($http);
        $e->remove('em/special&id');
    }

    public function testEmailsSetPrimaryPuts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/me/emails/em_123/primary')
            ->willReturn(['success' => true]);

        $e = new Emails($http);
        $result = $e->setPrimary('em_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testEmailsStartAddPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/emails/add', ['email' => 'user@example.com'])
            ->willReturn(['session_id' => 'ses_456']);

        $e = new Emails($http);
        $e->startAdd(['email' => 'user@example.com']);
    }

    public function testEmailsGetAddStatusGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/emails/add/ses_456')
            ->willReturn(['status' => 'pending']);

        $e = new Emails($http);
        $e->getAddStatus('ses_456');
    }

    public function testEmailsVerifyOtpPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/emails/add/ses_456/verify', ['code' => '123456'])
            ->willReturn($this->userEmailData());

        $e = new Emails($http);
        $result = $e->verifyOtp('ses_456', ['code' => '123456']);
        $this->assertInstanceOf(UserEmail::class, $result);
    }

    public function testEmailsResendOtpPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/emails/add/ses_456/resend')
            ->willReturn(['success' => true]);

        $e = new Emails($http);
        $result = $e->resendOtp('ses_456');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    // --- Assets ---

    public function testAssetsListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/assets')
            ->willReturn(['assets' => []]);

        $a = new Assets($http);
        $a->list();
    }

    public function testAssetsListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/assets', ['type' => 'phone', 'status' => 'active'])
            ->willReturn(['assets' => []]);

        $a = new Assets($http);
        $a->list(['type' => 'phone', 'status' => 'active']);
    }

    public function testAssetsGetGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/assets/asset_123')
            ->willReturn($this->userAssetData());

        $a = new Assets($http);
        $result = $a->get('asset_123');
        $this->assertInstanceOf(UserAsset::class, $result);
    }

    public function testAssetsRevokeDeletesById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/assets/asset_123')
            ->willReturn(['success' => true]);

        $a = new Assets($http);
        $result = $a->revoke('asset_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    // --- Auth ---

    public function testAuthGetMeGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/auth/me')
            ->willReturn(['id' => 'user_123', 'created_at' => '2025-01-01T00:00:00Z']);

        $a = new Auth($http);
        $result = $a->getMe();
        $this->assertInstanceOf(AuthUser::class, $result);
    }

    public function testAuthListSessionsGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/auth/sessions')
            ->willReturn(['sessions' => [], 'total' => 0]);

        $a = new Auth($http);
        $result = $a->listSessions();
        $this->assertInstanceOf(ListSessionsResponse::class, $result);
    }

    public function testAuthRevokeSessionDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/auth/sessions/sess_123')
            ->willReturn(['success' => true]);

        $a = new Auth($http);
        $result = $a->revokeSession('sess_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testAuthRevokeSessionEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/auth/sessions/sess%2Fspecial%26id')
            ->willReturn(['success' => true]);

        $a = new Auth($http);
        $a->revokeSession('sess/special&id');
    }

    // --- Settings ---

    public function testSettingsGetGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/settings')
            ->willReturn(['branding' => []]);

        $s = new Settings($http);
        $s->get();
    }

    public function testSettingsUpdatePatches(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/me/settings', ['branding' => ['business_name' => 'Acme']])
            ->willReturn(['branding' => ['business_name' => 'Acme']]);

        $s = new Settings($http);
        $s->update(['branding' => ['business_name' => 'Acme']]);
    }

    public function testSettingsGetUsageGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/usage', ['period' => '2026-02'])
            ->willReturn(['usage' => []]);

        $s = new Settings($http);
        $s->getUsage(['period' => '2026-02']);
    }

    public function testSettingsExportGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/export')
            ->willReturn(['data' => []]);

        $s = new Settings($http);
        $s->export();
    }

    // --- ApiKeys ---

    public function testApiKeysListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/api-keys')
            ->willReturn(['api_keys' => []]);

        $ak = new ApiKeys($http);
        $ak->list();
    }

    public function testApiKeysCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/api-keys', ['name' => 'My Key', 'environment' => 'test'])
            ->willReturn($this->apiKeyData());

        $ak = new ApiKeys($http);
        $result = $ak->create(['name' => 'My Key', 'environment' => 'test']);
        $this->assertInstanceOf(APIKeyResponse::class, $result);
    }

    public function testApiKeysCreatePostsEmptyBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/api-keys', [])
            ->willReturn($this->apiKeyData());

        $ak = new ApiKeys($http);
        $result = $ak->create();
        $this->assertInstanceOf(APIKeyResponse::class, $result);
    }

    public function testApiKeysRevokeDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/api-keys/key_123')
            ->willReturn(['success' => true]);

        $ak = new ApiKeys($http);
        $result = $ak->revoke('key_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testApiKeysRevokeEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/api-keys/key%2Fspecial%26id')
            ->willReturn(['success' => true]);

        $ak = new ApiKeys($http);
        $ak->revoke('key/special&id');
    }

    public function testApiKeysRegeneratePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/api-keys/key_123/regenerate')
            ->willReturn($this->apiKeyData());

        $ak = new ApiKeys($http);
        $result = $ak->regenerate('key_123');
        $this->assertInstanceOf(APIKeyResponse::class, $result);
    }

    // --- Account ---

    public function testAccountInitiateDeletionPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/account/delete', [])
            ->willReturn(['session_id' => 'sess_123']);

        $a = new Account($http);
        $a->initiateDeletion();
    }

    public function testAccountDeletionStatusGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/account/delete/sess_123')
            ->willReturn(['status' => 'pending']);

        $a = new Account($http);
        $a->deletionStatus('sess_123');
    }

    public function testAccountDeletionStatusEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/account/delete/sess%2Fspecial%26id')
            ->willReturn([]);

        $a = new Account($http);
        $a->deletionStatus('sess/special&id');
    }

    public function testAccountVerifyDeletionPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/account/delete/sess_123/verify', ['code' => '123456'])
            ->willReturn(['success' => true]);

        $a = new Account($http);
        $result = $a->verifyDeletion('sess_123', ['code' => '123456']);
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testAccountVerifyDeletionMagicLinkPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/account/delete/magic/tok_abc')
            ->willReturn(['success' => true]);

        $a = new Account($http);
        $result = $a->verifyDeletionMagicLink('tok_abc');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testAccountDeleteSendsDeleteWithBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/account', ['session_id' => 'sess_123'])
            ->willReturn(['success' => true]);

        $a = new Account($http);
        $result = $a->delete(['session_id' => 'sess_123']);
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    // --- TwoFA ---

    public function testTwoFAStartPostsWithParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/2fa/start', ['action_type' => 'api_key_view', 'channel' => 'email'])
            ->willReturn(['session_id' => 'sess_123']);

        $t = new TwoFA($http);
        $t->start(['action_type' => 'api_key_view', 'channel' => 'email']);
    }

    public function testTwoFAGetStatusGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/2fa/sess_123')
            ->willReturn(['status' => 'pending']);

        $t = new TwoFA($http);
        $t->getStatus('sess_123');
    }

    public function testTwoFAGetStatusEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/2fa/sess%2Fspecial%26id')
            ->willReturn([]);

        $t = new TwoFA($http);
        $t->getStatus('sess/special&id');
    }

    public function testTwoFAVerifyPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/2fa/sess_123/verify', ['code' => '123456'])
            ->willReturn(['success' => true]);

        $t = new TwoFA($http);
        $result = $t->verify('sess_123', ['code' => '123456']);
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testTwoFAVerifyMagicLinkPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/2fa/magic/tok_abc')
            ->willReturn(['success' => true]);

        $t = new TwoFA($http);
        $result = $t->verifyMagicLink('tok_abc');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    // --- DnsCredentials ---

    public function testDnsCredentialsListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/dns-credentials')
            ->willReturn(['data' => []]);

        $d = new DnsCredentials($http);
        $d->list();
    }

    public function testDnsCredentialsCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/dns-credentials', ['provider' => 'cloudflare', 'credentials' => ['api_token' => 'tok_123']])
            ->willReturn(['provider' => 'cloudflare']);

        $d = new DnsCredentials($http);
        $result = $d->create(['provider' => 'cloudflare', 'credentials' => ['api_token' => 'tok_123']]);
        $this->assertInstanceOf(DNSProviderCredentials::class, $result);
    }

    public function testDnsCredentialsDeleteDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/dns-credentials/cred_123')
            ->willReturn(['success' => true]);

        $d = new DnsCredentials($http);
        $result = $d->delete('cred_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testDnsCredentialsDeleteEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/dns-credentials/cred%2Fspecial%26id')
            ->willReturn(['success' => true]);

        $d = new DnsCredentials($http);
        $d->delete('cred/special&id');
    }

    // --- Domains ---

    public function testDomainsListGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/domains')
            ->willReturn(['data' => []]);

        $d = new Domains($http);
        $d->list();
    }

    public function testDomainsAddPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains', ['domain' => 'example.com', 'verification_method' => 'auto_dns'])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->add(['domain' => 'example.com', 'verification_method' => 'auto_dns']);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsGetGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/domains/dom_123')
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->get('dom_123');
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsGetEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/domains/dom%2Fspecial%26id')
            ->willReturn($this->domainData(['id' => 'dom/special&id']));

        $d = new Domains($http);
        $d->get('dom/special&id');
    }

    public function testDomainsDeleteDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/domains/dom_123')
            ->willReturn(['success' => true]);

        $d = new Domains($http);
        $d->delete('dom_123');
    }

    public function testDomainsOauthUrlPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/oauth-url')
            ->willReturn(['oauth_url' => 'https://example.com', 'state' => 'abc']);

        $d = new Domains($http);
        $d->oauthUrl('dom_123');
    }

    public function testDomainsVerifyPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify')
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->verify('dom_123');
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsConnectCloudflarePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/connect/cloudflare', ['api_token' => 'tok_cf'])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->connectCloudflare('dom_123', ['api_token' => 'tok_cf']);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsConnectGoDaddyPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/connect/godaddy', ['api_key' => 'key', 'api_secret' => 'secret'])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->connectGoDaddy('dom_123', ['api_key' => 'key', 'api_secret' => 'secret']);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsConnectProviderPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/connect/namecheap', ['credentials' => ['api_key' => 'k']])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->connectProvider('dom_123', 'namecheap', ['credentials' => ['api_key' => 'k']]);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsConnectProviderEncodesBothParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom%2Fid/connect/pro%2Fvider', ['credentials' => ['k' => 'v']])
            ->willReturn($this->domainData(['id' => 'dom/id']));

        $d = new Domains($http);
        $d->connectProvider('dom/id', 'pro/vider', ['credentials' => ['k' => 'v']]);
    }

    public function testDomainsAddProviderPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/add-provider/route53', ['credentials' => ['access_key' => 'ak']])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->addProvider('dom_123', 'route53', ['credentials' => ['access_key' => 'ak']]);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsAddProviderEncodesBothParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom%2Fid/add-provider/pro%2Fvider', ['credentials' => ['k' => 'v']])
            ->willReturn($this->domainData(['id' => 'dom/id']));

        $d = new Domains($http);
        $d->addProvider('dom/id', 'pro/vider', ['credentials' => ['k' => 'v']]);
    }

    public function testDomainsGetProvidersGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/dns-providers')
            ->willReturn(['cloudflare' => []]);

        $d = new Domains($http);
        $d->getProviders();
    }

    public function testDomainsVerifyWithCredentialsPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify-with-credentials')
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->verifyWithCredentials('dom_123');
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsCheckCredentialsGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/domains/dom_123/check-credentials')
            ->willReturn(['has_access' => true]);

        $d = new Domains($http);
        $d->checkCredentials('dom_123');
    }

    public function testDomainsStartEmailVerificationPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify-email', ['email_prefix' => 'admin'])
            ->willReturn(['success' => true]);

        $d = new Domains($http);
        $result = $d->startEmailVerification('dom_123', ['email_prefix' => 'admin']);
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testDomainsStartEmailVerificationDefaultParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify-email', [])
            ->willReturn(['success' => true]);

        $d = new Domains($http);
        $result = $d->startEmailVerification('dom_123');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testDomainsConfirmEmailCodePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify-email/confirm', ['code' => '123456'])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->confirmEmailCode('dom_123', ['code' => '123456']);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsResendEmailPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/verify-email/resend', ['email_prefix' => 'webmaster'])
            ->willReturn(['success' => true]);

        $d = new Domains($http);
        $result = $d->resendEmail('dom_123', ['email_prefix' => 'webmaster']);
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testDomainsEmailSetupPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/email-setup', ['from_email' => 'noreply@example.com'])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->emailSetup('dom_123', ['from_email' => 'noreply@example.com']);
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsEmailSetupDefaultParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/domains/dom_123/email-setup', [])
            ->willReturn($this->domainData());

        $d = new Domains($http);
        $result = $d->emailSetup('dom_123');
        $this->assertInstanceOf(Domain::class, $result);
    }

    public function testDomainsEmailStatusGets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/domains/dom_123/email-status')
            ->willReturn(['status' => 'active']);

        $d = new Domains($http);
        $d->emailStatus('dom_123');
    }

    // --- UserRequests ---

    public function testUserRequestsList(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/verification-requests')
            ->willReturn(['requests' => []]);

        $r = new UserRequests($http);
        $r->list();
    }

    public function testUserRequestsListIncoming(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/verification-requests/incoming')
            ->willReturn(['requests' => []]);

        $r = new UserRequests($http);
        $r->listIncoming();
    }

    public function testUserRequestsCreate(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verification-requests', ['type' => 'phone'])
            ->willReturn($this->verificationRequestData(['id' => 'req_1']));

        $r = new UserRequests($http);
        $result = $r->create(['type' => 'phone']);
        $this->assertInstanceOf(VerificationRequest::class, $result);
    }

    public function testUserRequestsClaim(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verification-requests/req_1/claim')
            ->willReturn(['success' => true]);

        $r = new UserRequests($http);
        $result = $r->claim('req_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testUserRequestsCancel(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/verification-requests/req_1')
            ->willReturn(['success' => true]);

        $r = new UserRequests($http);
        $result = $r->cancel('req_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testUserRequestsExtend(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verification-requests/req_1/extend')
            ->willReturn(['success' => true]);

        $r = new UserRequests($http);
        $result = $r->extend('req_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testUserRequestsShareEmail(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verification-requests/req_1/share-email')
            ->willReturn(['success' => true]);

        $r = new UserRequests($http);
        $result = $r->shareEmail('req_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testUserRequestsEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verification-requests/req%2Fspecial/claim')
            ->willReturn(['success' => true]);

        $r = new UserRequests($http);
        $r->claim('req/special');
    }

    // --- UserDomainVerify ---

    public function testUserDomainVerifyStart(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verify/domain', ['domain' => 'example.com'])
            ->willReturn(['sessionId' => 'sess_1']);

        $d = new UserDomainVerify($http);
        $d->start(['domain' => 'example.com']);
    }

    public function testUserDomainVerifyStatus(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/verify/domain/sess_1')
            ->willReturn(['status' => 'pending']);

        $d = new UserDomainVerify($http);
        $d->status('sess_1');
    }

    public function testUserDomainVerifyCheck(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/verify/domain/sess_1/check')
            ->willReturn(['success' => true]);

        $d = new UserDomainVerify($http);
        $d->check('sess_1');
    }

    public function testUserDomainVerifyEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/verify/domain/sess%2Fspecial')
            ->willReturn(['status' => 'pending']);

        $d = new UserDomainVerify($http);
        $d->status('sess/special');
    }

    // --- PublicProfiles ---

    public function testPublicProfilesGetById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/prof_1')
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->getById('prof_1');
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesGetAvatar(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/prof_1/avatar')
            ->willReturn(['url' => 'https://...']);

        $p = new PublicProfiles($http);
        $p->getAvatar('prof_1');
    }

    public function testPublicProfilesGetByUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/u/alice')
            ->willReturn($this->publicProfileData(['username' => 'alice']));

        $p = new PublicProfiles($http);
        $result = $p->getByUsername('alice');
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesCheckUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/check-username/alice')
            ->willReturn(['available' => true]);

        $p = new PublicProfiles($http);
        $p->checkUsername('alice');
    }

    public function testPublicProfilesListProfiles(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/profiles')
            ->willReturn(['profiles' => []]);

        $p = new PublicProfiles($http);
        $p->listProfiles();
    }

    public function testPublicProfilesCreateProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/profiles/profiles', ['name' => 'Test'])
            ->willReturn($this->publicProfileData(['id' => 'prof_2']));

        $p = new PublicProfiles($http);
        $result = $p->createProfile(['name' => 'Test']);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesGetProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/profiles/prof_1')
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->getProfile('prof_1');
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesUpdateProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/profiles/profiles/prof_1', ['name' => 'Updated'])
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->updateProfile('prof_1', ['name' => 'Updated']);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesDeleteProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/profiles/profiles/prof_1')
            ->willReturn(['success' => true]);

        $p = new PublicProfiles($http);
        $result = $p->deleteProfile('prof_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testPublicProfilesSetPrimary(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/profiles/profiles/prof_1/primary')
            ->willReturn(['success' => true]);

        $p = new PublicProfiles($http);
        $result = $p->setPrimary('prof_1');
        $this->assertInstanceOf(SuccessResponse::class, $result);
    }

    public function testPublicProfilesUpdateProfileProofs(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/profiles/profiles/prof_1/proofs', ['proofs' => ['p_1']])
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->updateProfileProofs('prof_1', ['proofs' => ['p_1']]);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesGetMyProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/me')
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->getMyProfile();
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesUpdateMyProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/profiles/me', ['bio' => 'Hello'])
            ->willReturn($this->publicProfileData(['id' => 'prof_1']));

        $p = new PublicProfiles($http);
        $result = $p->updateMyProfile(['bio' => 'Hello']);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesClaimUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/profiles/me/username', ['username' => 'alice'])
            ->willReturn($this->publicProfileData(['username' => 'alice']));

        $p = new PublicProfiles($http);
        $result = $p->claimUsername(['username' => 'alice']);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesGetAvailableAssets(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/me/assets')
            ->willReturn(['assets' => []]);

        $p = new PublicProfiles($http);
        $p->getAvailableAssets();
    }

    public function testPublicProfilesUpdatePublicProofs(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/profiles/me/proofs', ['proofs' => ['p_1']])
            ->willReturn($this->publicProfileData());

        $p = new PublicProfiles($http);
        $result = $p->updatePublicProofs(['proofs' => ['p_1']]);
        $this->assertInstanceOf(PublicProfile::class, $result);
    }

    public function testPublicProfilesEncodesProfileId(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/prof%2Fspecial')
            ->willReturn($this->publicProfileData(['id' => 'prof/special']));

        $p = new PublicProfiles($http);
        $p->getById('prof/special');
    }

    public function testPublicProfilesEncodesUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/check-username/user%2Fspecial')
            ->willReturn(['available' => true]);

        $p = new PublicProfiles($http);
        $p->checkUsername('user/special');
    }

    // --- Proofs ---

    public function testProofsValidatePosts(): void
    {
        $http = $this->mockHttp();
        $this->setMockProperty($http, 'baseUrl', 'https://api.proof.holdings');
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/proofs/validate', ['proof_token' => 'tok_123'])
            ->willReturn(['valid' => true]);

        $p = new Proofs($http);
        $result = $p->validate('tok_123');
        $this->assertInstanceOf(ValidateProofResponse::class, $result);
    }

    public function testProofsRevokePosts(): void
    {
        $http = $this->mockHttp();
        $this->setMockProperty($http, 'baseUrl', 'https://api.proof.holdings');
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/proofs/ver_123/revoke', ['reason' => 'test'])
            ->willReturn(['success' => true, 'revoked_at' => '2025-01-01T00:00:00Z']);

        $p = new Proofs($http);
        $result = $p->revoke('ver_123', 'test');
        $this->assertInstanceOf(RevokeProofResponse::class, $result);
    }

    public function testProofsStatusGets(): void
    {
        $http = $this->mockHttp();
        $this->setMockProperty($http, 'baseUrl', 'https://api.proof.holdings');
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/proofs/ver_123/status')
            ->willReturn([
                'proof_id' => 'ver_123',
                'status' => 'valid',
                'is_valid' => true,
                'is_revoked' => false,
                'revoked_at' => null,
                'revoked_reason' => null,
                'expires_at' => '2025-12-01T00:00:00Z',
                'is_expired' => false,
            ]);

        $p = new Proofs($http);
        $result = $p->status('ver_123');
        $this->assertInstanceOf(ProofStatusResponse::class, $result);
    }

    public function testProofsListRevokedGets(): void
    {
        $http = $this->mockHttp();
        $this->setMockProperty($http, 'baseUrl', 'https://api.proof.holdings');
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/proofs/revoked')
            ->willReturn(['revoked' => [], 'generated_at' => '2025-01-01T00:00:00Z', 'signature' => 'sig_123']);

        $p = new Proofs($http);
        $result = $p->listRevoked();
        $this->assertInstanceOf(RevocationList::class, $result);
    }
}

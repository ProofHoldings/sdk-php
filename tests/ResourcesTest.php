<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Confirmations;
use ProofHoldings\Resources\Hitl;
use ProofHoldings\Resources\HitlChatId;
use ProofHoldings\Resources\Verifications;
use ProofHoldings\Resources\Profiles;
use ProofHoldings\Resources\PublicProfiles;
use ProofHoldings\Resources\Sessions;
use ProofHoldings\Resources\Templates;
use ProofHoldings\Resources\VerificationRequests;
use ProofHoldings\Resources\WebhookDeliveries;
use ProofHoldings\Resources\Me;

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

    public function testVerificationsCreateMultiChannelPostsToEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/verifications/multi-channel', ['type' => 'phone', 'identifier' => '+1234567890', 'channels' => ['whatsapp', 'sms']])
            ->willReturn(['group_id' => 'vg_abc', 'status' => 'pending']);

        $v = new Verifications($http);
        $result = $v->createMultiChannel(['type' => 'phone', 'identifier' => '+1234567890', 'channels' => ['whatsapp', 'sms']]);
        $this->assertSame('vg_abc', $result['group_id']);
    }

    public function testVerificationsGetMultiChannelStatusGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/multi-channel/vg_abc123/status')
            ->willReturn(['group_id' => 'vg_abc123', 'status' => 'pending', 'winning_channel' => null, 'channels' => []]);

        $v = new Verifications($http);
        $result = $v->getMultiChannelStatus('vg_abc123');
        $this->assertSame('pending', $result['status']);
    }

    public function testVerificationsGetMultiChannelStatusEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verifications/multi-channel/vg%2Fspecial%26id/status')
            ->willReturn([]);

        $v = new Verifications($http);
        $v->getMultiChannelStatus('vg/special&id');
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

    // --- Hitl ---

    /** Minimal API-shaped HITL config payload that Hitl DTOs can parse. */
    private function hitlPayload(array $overrides = []): array
    {
        return array_merge([
            'id' => 'hitl_123',
            'user_id' => 'user_1',
            'name' => 'Production Approvals',
            'profile_id' => null,
            'channels' => [['type' => 'telegram', 'config' => ['chat_id' => '12345']]],
            'timeout_seconds' => 3600,
            'status' => 'pending',
            'keypair' => null,
            'environment' => 'production',
            'created_at' => '2026-06-07T00:00:00Z',
            'updated_at' => '2026-06-07T00:00:00Z',
        ], $overrides);
    }

    public function testHitlCreatePostsToEndpoint(): void
    {
        $params = [
            'name' => 'Production Approvals',
            'channels' => [['type' => 'telegram', 'config' => ['chat_id' => '12345']]],
            'timeout_seconds' => 3600,
        ];
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/hitl', $params)
            ->willReturn($this->hitlPayload());

        $h = new Hitl($http);
        $result = $h->create($params);
        $this->assertSame('hitl_123', $result->id);
        $this->assertSame('pending', $result->status);
    }

    public function testHitlListPassesFilters(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/hitl', ['status' => 'active', 'limit' => 10])
            ->willReturn([
                'data' => [],
                'pagination' => ['page' => 1, 'limit' => 10, 'total' => 0, 'total_pages' => 0],
                'limits' => ['max_active_configs' => 1, 'active_configs' => 0],
            ]);

        $h = new Hitl($http);
        $result = $h->list(['status' => 'active', 'limit' => 10]);
        $this->assertSame([], $result->data);
        $this->assertSame(['max_active_configs' => 1, 'active_configs' => 0], $result->limits);
    }

    public function testHitlGetEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/hitl/hitl%2Fspecial%26id')
            ->willReturn($this->hitlPayload());

        $h = new Hitl($http);
        $h->get('hitl/special&id');
    }

    public function testHitlUpdatePatchesBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/hitl/hitl_123', ['name' => 'Renamed', 'timeout_seconds' => 7200])
            ->willReturn($this->hitlPayload(['name' => 'Renamed', 'timeout_seconds' => 7200]));

        $h = new Hitl($http);
        $result = $h->update('hitl_123', ['name' => 'Renamed', 'timeout_seconds' => 7200]);
        $this->assertSame('Renamed', $result->name);
    }

    public function testHitlArchiveDeletes(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/hitl/hitl_123')
            ->willReturn($this->hitlPayload(['status' => 'archived']));

        $h = new Hitl($http);
        $result = $h->archive('hitl_123');
        $this->assertSame('archived', $result->status);
    }

    public function testHitlFinalizePostsWithoutBody(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/hitl/hitl_123/finalize')
            ->willReturn($this->hitlPayload([
                'status' => 'active',
                'authorizations' => [
                    ['channel' => 'telegram', 'recipient' => '12345', 'authorization_id' => 'auth_1', 'status' => 'pending'],
                ],
            ]));

        $h = new Hitl($http);
        $result = $h->finalize('hitl_123');
        $this->assertSame('active', $result->status);
        $this->assertCount(1, $result->authorizations);
    }

    public function testHitlRequestAuthorizationPostsToAuthorize(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/hitl/hitl%2Fspecial%26id/authorize')
            ->willReturn(['hitl_id' => 'hitl_123', 'authorizations' => [], 'message' => 'ok']);

        $h = new Hitl($http);
        $result = $h->requestAuthorization('hitl/special&id');
        $this->assertSame('hitl_123', $result->hitl_id);
    }

    // --- HitlChatId ---

    public function testHitlChatIdCreatePosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/hitl/chat-id-discovery')
            ->willReturn([
                'token' => 'tok',
                'deep_link' => 'https://t.me/x',
                'qr_code' => 'data:image/png;base64,x',
                'qr_text' => 'qr',
                'expires_in' => 600,
            ]);

        $d = new HitlChatId($http);
        $result = $d->create();
        $this->assertSame('tok', $result->token);
    }

    // --- Confirmations approval link ---

    public function testConfirmationsApprovalLinkPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/confirmations/conf%2Fspecial%26id/approval-link')
            ->willReturn(['approval_url' => 'https://api.example.com/resolve?t=x']);

        $c = new Confirmations($http);
        $result = $c->approvalLink('conf/special&id');
        $this->assertSame('https://api.example.com/resolve?t=x', $result->approval_url);
    }

    // --- Confirmations create / retrieve (shared wire fixture) ---

    /** @return array<string, mixed> */
    private function confirmationFixture(): array
    {
        $raw = json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/confirmation_approved.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        return array_filter($raw, fn (string $key) => !str_starts_with($key, '_'), ARRAY_FILTER_USE_KEY);
    }

    public function testConfirmationsRetrieveDeserializesTheWireShape(): void
    {
        $fixture = $this->confirmationFixture();
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/confirmations/' . $fixture['id'])
            ->willReturn($fixture);

        $result = (new Confirmations($http))->retrieve($fixture['id']);

        $this->assertSame($fixture['id'], $result->id);
        $this->assertSame('approved', $result->status);
        $this->assertSame($fixture['proof_token'], $result->proof_token);
        $this->assertSame($fixture['response'], $result->response);
    }

    public function testConfirmationsCreateDeserializesTheWireShape(): void
    {
        $fixture = $this->confirmationFixture();
        $params = ['hitl_id' => $fixture['hitl_id'], 'message' => $fixture['message']];
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/confirmations', $params)
            ->willReturn($fixture);

        $result = (new Confirmations($http))->create($params);

        $this->assertSame($fixture['id'], $result->id);
        $this->assertSame($fixture['proof_token'], $result->proof_token);
        $this->assertSame($fixture['response'], $result->response);
    }

    public function testWebhookPayloadHydratesAProofWhoseIdentifierAndChannelAreNull(): void
    {
        $payload = \ProofHoldings\Types\WebhookPayload::fromArray([
            'event' => 'verification_request.completed',
            'request_id' => 'vr_1',
            'status' => 'completed',
            'action_type' => 'verification',
            'proofs' => [[
                'type' => 'phone',
                'identifier' => null,
                'verified_at' => '2026-09-29T10:00:00.000Z',
                'channel' => null,
                'proof_token' => 'jwt',
            ]],
            'progress' => ['completed' => 1, 'required' => 1, 'total' => 1],
        ]);

        $this->assertInstanceOf(\ProofHoldings\Types\WebhookProof::class, $payload->proofs[0]);
        $this->assertNull($payload->proofs[0]->identifier);
        $this->assertNull($payload->proofs[0]->channel);
    }

    public function testConfirmationsListHydratesEachItem(): void
    {
        $fixture = $this->confirmationFixture();
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/confirmations', ['status' => 'approved'])
            ->willReturn([
                'data' => [$fixture],
                'pagination' => ['page' => 1, 'limit' => 20, 'total' => 1, 'total_pages' => 1],
            ]);

        $result = (new Confirmations($http))->list(['status' => 'approved']);

        $this->assertCount(1, $result->data);
        $this->assertInstanceOf(\ProofHoldings\Types\Confirmation::class, $result->data[0]);
        $this->assertSame($fixture['id'], $result->data[0]->id);
        $this->assertSame('approved', $result->data[0]->status);
    }

    // --- VerificationRequests proofs ---

    public function testVerificationRequestsGetProofsGetsById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/verification-requests/vr%2Fspecial%26id/proofs')
            ->willReturn(['proofs' => []]);

        $vr = new VerificationRequests($http);
        $result = $vr->getProofs('vr/special&id');
        $this->assertSame([], $result['proofs']);
    }

    // --- Templates ---

    public function testTemplatesListGetsEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates')
            ->willReturn(['custom_templates' => [], 'available_channels' => []]);

        $t = new Templates($http);
        $result = $t->list();
        $this->assertSame([], $result['custom_templates']);
    }

    public function testTemplatesGetDefaults(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates/defaults')
            ->willReturn(['defaults' => []]);

        $t = new Templates($http);
        $result = $t->getDefaults();
        $this->assertSame([], $result['defaults']);
    }

    public function testTemplatesGetByChannelAndType(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/templates/telegram/confirmation_request')
            ->willReturn(['is_custom' => false, 'template' => []]);

        $t = new Templates($http);
        $result = $t->get('telegram', 'confirmation_request');
        $this->assertFalse($result['is_custom']);
    }

    public function testTemplatesUpsertPutsContent(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with('/api/v1/templates/whatsapp/verification_request', [
                'body' => 'Your code is {{code}}',
                'button_text' => 'Verify',
            ])
            ->willReturn(['success' => true, 'template' => []]);

        $t = new Templates($http);
        $result = $t->upsert('whatsapp', 'verification_request', [
            'body' => 'Your code is {{code}}',
            'button_text' => 'Verify',
        ]);
        $this->assertTrue($result['success']);
    }

    public function testTemplatesDeleteResetsToDefault(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/templates/email/login_request')
            ->willReturn(['success' => true, 'message' => 'Template reset to default']);

        $t = new Templates($http);
        $result = $t->delete('email', 'login_request');
        $this->assertTrue($result['success']);
    }

    public function testTemplatesPreviewPostsContent(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/templates/preview', [
                'channel' => 'telegram',
                'message_type' => 'confirmation_request',
                'body' => 'Hi {{business_name}}',
            ])
            ->willReturn(['preview' => ['body' => 'x'], 'validation_errors' => [], 'is_valid' => true]);

        $t = new Templates($http);
        $result = $t->preview([
            'channel' => 'telegram',
            'message_type' => 'confirmation_request',
            'body' => 'Hi {{business_name}}',
        ]);
        $this->assertTrue($result['is_valid']);
    }

    public function testTemplatesRenderPostsVariables(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/templates/render', [
                'channel' => 'sms',
                'message_type' => 'verification_request',
                'variables' => ['code' => '123456'],
            ])
            ->willReturn(['rendered' => ['body' => 'Code: 123456']]);

        $t = new Templates($http);
        $result = $t->render([
            'channel' => 'sms',
            'message_type' => 'verification_request',
            'variables' => ['code' => '123456'],
        ]);
        $this->assertSame('Code: 123456', $result['rendered']['body']);
    }

    // --- Profiles ---

    public function testProfilesListGetsEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles')
            ->willReturn(['profiles' => [], 'count' => 0, 'max_profiles' => 1]);

        $p = new Profiles($http);
        $result = $p->list();
        $this->assertSame(0, $result['count']);
    }

    public function testProfilesCreatePostsParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/profiles', ['display_name' => 'Acme', 'is_business' => true])
            ->willReturn(['id' => 'pr_1']);

        $p = new Profiles($http);
        $result = $p->create(['display_name' => 'Acme', 'is_business' => true]);
        $this->assertSame('pr_1', $result['id']);
    }

    public function testProfilesGetEncodesId(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles/pr%2Fspecial%26id')
            ->willReturn(['id' => 'pr_1']);

        $p = new Profiles($http);
        $p->get('pr/special&id');
    }

    public function testProfilesUpdatePatchesProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('patch')
            ->with('/api/v1/me/profiles/pr_1', ['bio' => 'New bio'])
            ->willReturn(['id' => 'pr_1']);

        $p = new Profiles($http);
        $p->update('pr_1', ['bio' => 'New bio']);
    }

    public function testProfilesDeleteDeletesProfile(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/profiles/pr_1')
            ->willReturn(['success' => true, 'message' => 'Profile deleted']);

        $p = new Profiles($http);
        $result = $p->delete('pr_1');
        $this->assertTrue($result['success']);
    }

    public function testProfilesSetPrimaryPosts(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/profiles/pr_1/primary')
            ->willReturn(['id' => 'pr_1', 'is_primary' => true]);

        $p = new Profiles($http);
        $result = $p->setPrimary('pr_1');
        $this->assertTrue($result['is_primary']);
    }

    public function testProfilesListTemplates(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/profiles/pr_1/templates')
            ->willReturn(['profile_id' => 'pr_1', 'templates' => []]);

        $p = new Profiles($http);
        $result = $p->listTemplates('pr_1');
        $this->assertSame([], $result['templates']);
    }

    public function testProfilesUpdateTemplatePutsContent(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('put')
            ->with(
                '/api/v1/me/profiles/pr_1/templates/telegram/confirmation_request',
                ['body' => 'Approve {{business_name}}?'],
            )
            ->willReturn(['channel' => 'telegram', 'message_type' => 'confirmation_request', 'is_custom' => true]);

        $p = new Profiles($http);
        $result = $p->updateTemplate('pr_1', 'telegram', 'confirmation_request', ['body' => 'Approve {{business_name}}?']);
        $this->assertTrue($result['is_custom']);
    }

    public function testProfilesDeleteTemplate(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('delete')
            ->with('/api/v1/me/profiles/pr_1/templates/email/login_request')
            ->willReturn(['channel' => 'email', 'message_type' => 'login_request', 'is_custom' => false]);

        $p = new Profiles($http);
        $result = $p->deleteTemplate('pr_1', 'email', 'login_request');
        $this->assertFalse($result['is_custom']);
    }

    public function testProfilesPreviewTemplatePostsContent(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('post')
            ->with('/api/v1/me/profiles/pr_1/templates/preview', [
                'channel' => 'sms',
                'message_type' => '2fa_request',
                'body' => 'Code: {{code}}',
            ])
            ->willReturn(['preview' => ['body' => 'x'], 'variables_used' => []]);

        $p = new Profiles($http);
        $result = $p->previewTemplate('pr_1', [
            'channel' => 'sms',
            'message_type' => '2fa_request',
            'body' => 'Code: {{code}}',
        ]);
        $this->assertSame([], $result['variables_used']);
    }

    // --- PublicProfiles ---

    public function testPublicProfilesGetById(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/pr_1')
            ->willReturn(['profile_id' => 'pr_1', 'proofs' => [], 'links' => []]);

        $p = new PublicProfiles($http);
        $result = $p->getById('pr_1');
        $this->assertSame('pr_1', $result['profile_id']);
    }

    public function testPublicProfilesGetByIdEncodesId(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/pr%2Fspecial%26id')
            ->willReturn([]);

        $p = new PublicProfiles($http);
        $p->getById('pr/special&id');
    }

    public function testPublicProfilesGetAvatar(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/p/pr_1/avatar')
            ->willReturn(['data' => "\x89PNG", 'content_type' => 'image/png']);

        $p = new PublicProfiles($http);
        $result = $p->getAvatar('pr_1');
        $this->assertSame('image/png', $result['content_type']);
    }

    public function testPublicProfilesGetByUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/u/acme')
            ->willReturn(['profile_id' => 'pr_1', 'username' => 'acme']);

        $p = new PublicProfiles($http);
        $result = $p->getByUsername('acme');
        $this->assertSame('acme', $result['username']);
    }

    public function testPublicProfilesGetBadge(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/badge/team_mgmt')
            ->willReturn(['username' => 'team_mgmt', 'state' => 'no_public_record']);

        $p = new PublicProfiles($http);
        $result = $p->getBadge('team_mgmt');
        // `state` is what a caller branches on: no public record still answers 200, never a 404.
        $this->assertSame('no_public_record', $result['state']);
    }

    public function testPublicProfilesCheckUsername(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/check-username/acme')
            ->willReturn(['available' => true]);

        $p = new PublicProfiles($http);
        $result = $p->checkUsername('acme');
        $this->assertTrue($result['available']);
    }

    // --- Me (dashboard/agent data surface) ---

    public function testMeSummaryGetsWithEnvironment(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/summary', ['environment' => 'test'])
            ->willReturn(['as_of' => '2026-06-22', 'active_api_keys' => 1]);

        $m = new Me($http);
        $result = $m->summary('test');
        $this->assertSame(1, $result['active_api_keys']);
    }

    public function testMeSummaryGetsWithoutEnvironment(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/summary', [])
            ->willReturn(['as_of' => '2026-06-22']);

        $m = new Me($http);
        $m->summary();
    }

    public function testMeSearchGetsWithQueryParams(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/search', ['q' => 'acme', 'type' => 'verifications', 'limit' => 5])
            ->willReturn(['data' => [], 'total' => 0]);

        $m = new Me($http);
        $m->search('acme', 'verifications', null, 5);
    }

    public function testMeApiKeySelfGetsSingularEndpoint(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/api-key')
            ->willReturn(['id' => 'key_1', 'key_prefix' => 'pk_live_']);

        $m = new Me($http);
        $result = $m->apiKeySelf();
        $this->assertSame('key_1', $result['id']);
    }

    public function testMeApiKeyUsageGetsWithEncodedIdAndEnvironment(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/api-keys/key_1/usage', ['environment' => 'production'])
            ->willReturn(['api_key' => [], 'verifications' => []]);

        $m = new Me($http);
        $m->apiKeyUsage('key_1', 'production');
    }

    public function testMeApiKeyUsageEncodesSpecialChars(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())
            ->method('get')
            ->with('/api/v1/me/api-keys/key%2Fspecial%26id/usage', [])
            ->willReturn([]);

        $m = new Me($http);
        $m->apiKeyUsage('key/special&id');
    }
}

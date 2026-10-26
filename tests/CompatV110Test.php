<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\Types\BrandingSettings;
use ProofHoldings\Types\CreateAuthorizationBody;
use ProofHoldings\Types\CreateMultiChannelVerificationRequest;
use ProofHoldings\Types\CreateSessionRequest;
use ProofHoldings\Types\CreateVerificationRequest;
use ProofHoldings\Types\HealthResponse;
use ProofHoldings\Types\IdentifierRemovedResponse;
use ProofHoldings\Types\ProfileTemplate;
use ProofHoldings\Types\PublicProfile;
use ProofHoldings\Types\Session;
use ProofHoldings\Types\SettingsResponse;
use ProofHoldings\Types\UpdateSettingsRequest;
use ProofHoldings\Types\UpdateSettingsResponse;
use ProofHoldings\Types\WebhookDelivery;
use ProofHoldings\Types\WebhookSettingsResponse;

/**
 * Code written against proof-holdings/sdk 1.1.0 must keep working on every 1.x release. Each case
 * builds a type the way 1.1.0 code could — POSITIONALLY, in 1.1.0's parameter order — and checks
 * every value lands where it did. A later API change removed or reshaped these types; the generator
 * keeps their 1.1.0 constructors (new parameters only ever appended, removed ones deprecated)
 * instead of re-sorting them. If a case fails, a 1.x release would break its users.
 */
class CompatV110Test extends TestCase
{
    public function testRemovedTypesStillConstruct(): void
    {
        $branding = new BrandingSettings('Acme', 'https://acme.test/logo.png', '#123456', 'help@acme.test', 'light');
        $this->assertSame('Acme', $branding->business_name);
        $this->assertSame('light', $branding->email_theme);

        $template = new ProfileTemplate('email', 'verification', 'Body', true, false, 'Subject', 'Go', 'https://x.test/{id}');
        $this->assertSame('verification', $template->message_type);
        $this->assertFalse($template->is_active);
        $this->assertSame('https://x.test/{id}', $template->button_url_template);
    }

    public function testCreateAuthorizationBodyKeepsItsPositionalOrder(): void
    {
        $body = new CreateAuthorizationBody('hitl_1', 'recipient', '+15550001111', 'telegram', 'Acme', 'bsuid_1', 'scope', 'bp_1', 600);
        $this->assertSame('telegram', $body->channel);
        $this->assertSame('Acme', $body->business_name);
        $this->assertSame('bsuid_1', $body->bsuid);
        $this->assertSame('scope', $body->scope);
        $this->assertSame(600, $body->expires_in);
    }

    public function testVerificationRequestsKeepTheirPositionalOrder(): void
    {
        $multi = new CreateMultiChannelVerificationRequest('phone', '+15550001111', ['sms'], 'user_1', ['k' => 'v'], 30);
        $this->assertSame('user_1', $multi->external_user_id);
        $this->assertSame(['k' => 'v'], $multi->client_metadata);
        $this->assertSame(30, $multi->proof_expiry_days);

        $single = new CreateVerificationRequest('email', 'email', 'a@b.test', 'user_1', ['k' => 'v'], null, 'prefix', 'bot', 30);
        $this->assertSame('user_1', $single->external_user_id);
        $this->assertSame('prefix', $single->email_prefix);
        $this->assertSame('bot', $single->bot_token);
        $this->assertSame(30, $single->proof_expiry_days);
    }

    public function testSessionTypesKeepTheirPositionalOrder(): void
    {
        $request = new CreateSessionRequest('+15550001111', 'sms');
        $this->assertSame('+15550001111', $request->phone);
        $this->assertSame('sms', $request->channel);

        $session = new Session('sess_1', 'pending', '2026-10-01T00:00:00Z', '2026-10-01T00:10:00Z');
        $this->assertSame('pending', $session->status);
        $this->assertSame('2026-10-01T00:10:00Z', $session->expires_at);
    }

    public function testSessionFromArrayReadsTodaysResponse(): void
    {
        $session = Session::fromArray([
            'id' => 'sess_1',
            'channel' => 'sms',
            'status' => 'verified',
            'created_at' => '2026-10-01T00:00:00Z',
            'phone_number' => '+15550001111',
        ]);
        $this->assertSame('sms', $session->channel);
        $this->assertNull($session->expires_at);
    }

    public function testSettingsTypesKeepTheirPositionalOrder(): void
    {
        $branding = new BrandingSettings('Acme');
        $webhook = WebhookSettingsResponse::fromArray(['url' => null, 'events' => [], 'secret_set' => false]);

        $settings = new SettingsResponse($branding, $webhook, 30);
        $this->assertSame(30, $settings->proof_expiry_days);

        $updated = new UpdateSettingsResponse(true, $branding, $webhook, 30);
        $this->assertSame($webhook, $updated->webhook);
        $this->assertSame(30, $updated->proof_expiry_days);

        $request = new UpdateSettingsRequest($branding, null, 30);
        $this->assertSame($branding, $request->branding);
    }

    public function testResponseTypesKeepTheirPositionalOrder(): void
    {
        $health = new HealthResponse('ok', 1700000000);
        $this->assertSame(1700000000, $health->timestamp);

        $removed = new IdentifierRemovedResponse('true', 'phone', 'id_1', '+15550001111', 1, 2, [], 3, null, 'none', 4, ['n']);
        $this->assertSame(4, $removed->sessions_invalidated);
        $this->assertSame(['n'], $removed->notice);

        $delivery = new WebhookDelivery('d_1', null, 'proof.verified', 'https://x.test', 'delivered', 1, 5, null, null, null, 200, null, '2026-10-01T00:00:00Z', ['a' => 1], 'ok', '2026-10-01T00:00:01Z');
        $this->assertSame(['a' => 1], $delivery->payload);
        $this->assertSame('ok', $delivery->response_body);
        $this->assertSame('2026-10-01T00:00:01Z', $delivery->updated_at);

        $profile = new PublicProfile('p_1', true, 'c', 'u', 'Name', 'Bio', null, 'Acme', true, true, 'acme', null, [], [], true, ['d']);
        $this->assertSame(['d'], $profile->delegations);
        $this->assertTrue($profile->show_delegations);
    }

    public function testIdentifierRemovedFromArrayReadsA110Payload(): void
    {
        $removed = IdentifierRemovedResponse::fromArray([
            'success' => 'true', 'type' => 'phone', 'removed_id' => 'id_1', 'identifier' => '+15550001111',
            'proofs_revoked' => 0, 'authorizations_revoked' => 0, 'hitl_configs_without_approver' => [],
            'circles_repointed' => 0, 'new_primary' => null, 'lookalike_watch' => 'none',
            'sessions_invalidated' => 0, 'notice' => [],
        ]);
        $this->assertNull($removed->sessions_signed_out);
    }
}

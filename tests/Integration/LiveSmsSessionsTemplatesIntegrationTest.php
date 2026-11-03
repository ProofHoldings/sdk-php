<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use ProofHoldings\Exceptions\ForbiddenException;

/**
 * Live coverage for what 1.2.0 adds: SMS sessions and per-project templates.
 *
 * Skips outside live mode (PROOF_BASE_URL + PROOF_API_KEY_TEST); runs against staging in the
 * staging deploy and production in the production deploy. Nothing here sends a message or writes a
 * row: creating an SMS session only stores a code the person would text in, and the template calls read.
 */
final class LiveSmsSessionsTemplatesIntegrationTest extends IntegrationTestCase
{
    /**
     * A well-formed project id no account owns: naming it must be refused, which is what proves
     * `$profileId` reaches the server rather than being dropped on the way.
     */
    private const UNOWNED_PROFILE_ID = '000000000000000000000000';

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::isLiveMode()) {
            $this->markTestSkipped('live mode only');
        }
    }

    public function testSmsSessionCarriesCodeMessageNumbersAndDeepLink(): void
    {
        $proof = $this->makeClient();

        $session = $proof->sessions->create([
            'channel' => 'sms',
            'client_metadata' => ['test_harness' => 'sdk-integration', 'sdk' => 'php'],
        ]);

        $this->assertNotEmpty($session['id'] ?? null);
        $this->assertSame('sms', $session['channel']);
        $this->assertSame('pending', $session['status']);
        $this->assertMatchesRegularExpression('/^\S+$/', $session['sms_code']);
        $this->assertStringContainsString($session['sms_code'], $session['sms_message']);
        $did = $session['sms_dids']['americas']['did'];
        $this->assertMatchesRegularExpression('/^\+\d+$/', $did);
        $this->assertSame('sms:' . $did . '?body=' . rawurlencode($session['sms_message']), $session['deep_link']);

        $fetched = $proof->sessions->retrieve($session['id']);
        $this->assertSame($session['id'], $fetched['id']);
        $this->assertSame('pending', $fetched['status']);
    }

    public function testTemplatesForTheKeysDefaultProject(): void
    {
        $proof = $this->makeClient();

        $listed = $proof->templates->list();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{24}$/', $listed['profile_id']);
        $this->assertIsArray($listed['custom_templates']);
        $this->assertContains('sms', $listed['available_channels']);

        $template = $proof->templates->get('telegram', 'verification_request');
        $this->assertSame($listed['profile_id'], $template['profile_id']);
        $this->assertContains($template['scope'], ['project', 'default']);
        $this->assertIsString($template['template']['body']);
    }

    public function testTemplatesForANamedProjectAndTheNameReachesTheServer(): void
    {
        $proof = $this->makeClient();
        $projectId = $proof->templates->list()['profile_id'];

        $this->assertSame($projectId, $proof->templates->list($projectId)['profile_id']);
        $this->assertSame($projectId, $proof->templates->get('telegram', 'verification_request', $projectId)['profile_id']);

        try {
            $proof->templates->list(self::UNOWNED_PROFILE_ID);
            $this->fail('naming an unowned project must be refused');
        } catch (ForbiddenException $refused) {
            $this->assertContains($refused->errorCode, ['profile_not_owned', 'profile_mismatch']);
        }
    }
}

<?php
/**
 * PHP SDK — Live API integration tests.
 *
 * Run against a real server using pk_test_* keys.
 * NOT included in the default test suite.
 *
 * Prerequisites:
 *   PROOF_API_KEY_TEST — Required, must start with "pk_test_"
 *   PROOF_BASE_URL     — Optional, defaults to "https://api.proof.holdings"
 *
 * Run:
 *   PROOF_API_KEY_TEST=pk_test_xxx vendor/bin/phpunit tests/Integration/
 */

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use PHPUnit\Framework\TestCase;
use ProofHoldings\Proof;

class VerificationTest extends TestCase
{
    private static ?Proof $client = null;

    public static function setUpBeforeClass(): void
    {
        $apiKey = getenv('PROOF_API_KEY_TEST') ?: '';
        if (!str_starts_with($apiKey, 'pk_test_')) {
            self::markTestSkipped('PROOF_API_KEY_TEST not set or not a test key');
        }
        $baseUrl = getenv('PROOF_BASE_URL') ?: 'https://api.proof.holdings';
        self::$client = new Proof($apiKey, $baseUrl);
    }

    private static int $counter = 0;

    private static function uniqueEmail(string $prefix): string
    {
        self::$counter++;
        return sprintf('%s-%d-%d@example.com', $prefix, time(), self::$counter);
    }

    public function testCreatePhoneVerification(): void
    {
        $verification = self::$client->verifications->create([
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => '+14155550100',
        ]);
        $this->assertNotEmpty($verification->id);
        $this->assertEquals('pending', $verification->status);
    }

    public function testListVerifications(): void
    {
        $result = self::$client->verifications->list(['limit' => 5]);
        $this->assertIsArray($result->data);
        $this->assertNotNull($result->pagination);
    }

    public function testRetrieveVerification(): void
    {
        $created = self::$client->verifications->create([
            'type' => 'email',
            'channel' => 'email',
            'identifier' => self::uniqueEmail('php-sdk-test'),
        ]);
        $retrieved = self::$client->verifications->retrieve($created->id);
        $this->assertEquals($created->id, $retrieved->id);
    }

    public function testVerifyAndValidateProof(): void
    {
        $verification = self::$client->verifications->create([
            'type' => 'email',
            'channel' => 'email',
            'identifier' => self::uniqueEmail('php-sdk-proof'),
        ]);
        $verified = self::$client->verifications->testVerify($verification->id);
        $this->assertNotEmpty($verified->proof_token);

        $proof = self::$client->proofs->validate($verified->proof_token);
        $this->assertTrue($proof->valid);
    }

    public function testCreateVerificationRequest(): void
    {
        $vr = self::$client->verificationRequests->create([
            'assets' => [['type' => 'email', 'identifier' => self::uniqueEmail('php-sdk-vr')]],
        ]);
        $this->assertNotEmpty($vr->id);
    }

    public function testListRevokedProofs(): void
    {
        $result = self::$client->proofs->listRevoked();
        $this->assertIsArray($result->revoked);
        $this->assertIsInt($result->count);
    }
}

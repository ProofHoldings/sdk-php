<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

/**
 * Proof-Me resource round-trip: identity challenge create + poll.
 */
class ProofMeIntegrationTest extends IntegrationTestCase
{
    private const string CIRCLE_ID = '0123456789abcdef01234567';
    private const string MEMBER_ID = 'fedcba9876543210fedcba98';
    private const string CHALLENGE_ID = 'aaaabbbbccccddddeeeeffff';

    /** @return array<string, mixed> */
    private static function challenge(): array
    {
        return [
            'id' => self::CHALLENGE_ID,
            'status' => 'pending',
            'confirm_channel' => 'whatsapp',
            'single_channel_warning' => false,
            'created_at' => '2026-06-23T00:00:00.000Z',
        ];
    }

    public function testIdentityChallengeRoundTrip(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped('live mode skips Proof-Me mutating flow');
        }

        $resolved = self::challenge();
        $resolved['status'] = 'active';

        $history = [];
        $proof = $this->makeClient([
            $this->jsonResponse(201, self::challenge()),
            $this->jsonResponse(200, $resolved),
        ], $history);

        $created = $proof->proofMe->createIdentityChallenge([
            'circle_id' => self::CIRCLE_ID,
            'member_id' => self::MEMBER_ID,
        ]);
        $this->assertSame('whatsapp', $created->confirm_channel);

        $sentBody = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertSame(
            ['circle_id' => self::CIRCLE_ID, 'member_id' => self::MEMBER_ID],
            $sentBody,
        );

        $fetched = $proof->proofMe->getIdentityChallenge(self::CHALLENGE_ID);
        $this->assertSame('active', $fetched->status);
    }
}

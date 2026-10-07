<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

/**
 * Resource round-trip: verification create + retrieve + list.
 *
 * All mock responses are built from the shared wire fixtures under
 * ``sdks/shared/fixtures/`` so contract drift surfaces in the backend
 * drift test (``src/__tests__/drift/sdk-fixtures.test.ts``) rather than
 * silently poisoning every SDK mock.
 */
class ResourcesIntegrationTest extends IntegrationTestCase
{
    public function testCreateAndRetrieveVerification(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped('live mode skips mutating SMS flow');
        }

        $verification = self::sharedFixture('verification.json');

        $proof = $this->makeClient([
            $this->jsonResponse(201, $verification),
            $this->jsonResponse(200, $verification),
        ]);

        $created = $proof->verifications->create([
            'type' => $verification['type'],
            'channel' => $verification['channel'],
            'identifier' => $verification['identifier'],
        ]);
        $this->assertSame($verification['id'], $created['id']);
        $this->assertSame($verification['status'], $created['status']);

        $fetched = $proof->verifications->retrieve($verification['id']);
        $this->assertSame($verification['identifier'], $fetched['identifier']);
        $this->assertSame($verification['created_at'], $fetched['created_at']);
    }

    public function testListReturnsPaginationEnvelope(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $listEnvelope = self::sharedFixture('verification_list.json');

        $proof = $this->makeClient([
            $this->jsonResponse(200, $listEnvelope),
        ]);

        $page = $proof->verifications->list();
        $this->assertIsArray($page['data']);
        $this->assertSame(1, $page['pagination']['page']);
        $this->assertArrayHasKey('total_pages', $page['pagination']);
    }
}

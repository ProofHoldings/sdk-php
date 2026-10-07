<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use ProofHoldings\Exceptions\NotFoundException;
use ProofHoldings\Exceptions\ValidationException;

/**
 * Deep live-mode lifecycle coverage for the PHP SDK.
 *
 * All tests here skip outside live mode. In live mode they exercise the
 * verification lifecycle against staging (or production on the production
 * branch pipeline) using a test-mode API key so /test-verify auto-completes
 * the verification without triggering real channel traffic.
 *
 * Created resources are tagged via client_metadata so the staging-data
 * pruner can reclaim them later.
 */
class LiveLifecycleIntegrationTest extends IntegrationTestCase
{
    private static function testRunId(): string
    {
        return getenv('CI_PIPELINE_ID')
            ?: (getenv('PROOF_TEST_RUN_ID')
                ?: 'local-' . (getenv('USER') ?: 'unknown') . '-' . time());
    }

    /** @return array<string, mixed> */
    private static function tagMetadata(array $extra = []): array
    {
        return array_merge(
            [
                'test_harness' => 'sdk-integration',
                'test_run_id' => self::testRunId(),
                'sdk' => 'php',
            ],
            $extra,
        );
    }

    /**
     * Real Proof-controlled phone number — staging SMS DID by default.
     * Override via TEST_PHONE_IDENTIFIER for other environments. The backend's
     * phone validator rejects reserved/fictional ranges, so fixtures must use
     * real numbers we control. Reusing one number across concurrent tests is
     * safe because the pk_test_* key path routes through /test-verify and
     * never triggers real SMS dispatch — the number is effectively an opaque
     * label, not a delivery target. Each verifications->create writes a
     * fresh document, and tests key their state off the returned id, not
     * off the phone.
     */
    private static function testPhoneIdentifier(): string
    {
        $v = getenv('TEST_PHONE_IDENTIFIER');
        return $v !== false && $v !== '' ? $v : '+19295909022';
    }

    private static function externalUserId(): string
    {
        return 'sdk_php_' . self::testRunId();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!self::isLiveMode()) {
            $this->markTestSkipped('live mode only');
        }
    }

    public function testVerificationLifecycleCreateRetrieveListTestVerify(): void
    {
        $proof = $this->makeClient();

        $created = $proof->verifications->create([
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => self::testPhoneIdentifier(),
            'external_user_id' => self::externalUserId(),
            'client_metadata' => self::tagMetadata(['stage' => 'lifecycle']),
        ]);
        $this->assertNotEmpty($created['id'] ?? null);
        $this->assertSame('pending', $created['status']);

        $fetched = $proof->verifications->retrieve($created['id']);
        $this->assertSame($created['id'], $fetched['id']);

        $listed = $proof->verifications->list(['limit' => 5]);
        $this->assertIsArray($listed['data']);
        $this->assertArrayHasKey('pagination', $listed);

        $verified = $proof->verifications->testVerify($created['id']);
        $this->assertSame('verified', $verified['status']);
        $this->assertTrue($verified['test_mode'] ?? false);
        $this->assertNotEmpty($verified['proof_token'] ?? null);
    }

    public function testRetrieveUnknownIdRaisesNotFound(): void
    {
        $proof = $this->makeClient();
        $this->expectException(NotFoundException::class);
        $proof->verifications->retrieve('000000000000000000000000');
    }

    public function testCreateRejectsInvalidPayload(): void
    {
        $proof = $this->makeClient();
        $this->expectException(ValidationException::class);
        $proof->verifications->create([
            'type' => 'phone',
            'channel' => 'not_a_real_channel',
            'identifier' => 'not-a-phone',
        ]);
    }

    public function testListPaginationEnvelopeShape(): void
    {
        $proof = $this->makeClient();
        $page = $proof->verifications->list(['page' => 1, 'limit' => 3]);
        $this->assertIsArray($page['data']);
        $this->assertIsArray($page['pagination']);
        foreach (['page', 'limit', 'total'] as $key) {
            $this->assertArrayHasKey($key, $page['pagination']);
        }
    }

    public function testVerifiedUsersReadOnly(): void
    {
        $proof = $this->makeClient();
        $result = $proof->verifications->listVerifiedUsers(['limit' => 3]);
        $this->assertIsArray($result['data']);
    }

    public function testWebhookDeliveriesListReadOnly(): void
    {
        $proof = $this->makeClient();
        $page = $proof->webhookDeliveries->list(['limit' => 3]);
        // NOTE: this endpoint is the one outlier in the API surface —
        // backend returns {deliveries: [...], pagination: {...}} instead
        // of the standard {data: [...], pagination: {...}}. See
        // src/controllers/webhookDeliveries.ts.
        $this->assertArrayHasKey('deliveries', $page, 'webhook-deliveries response envelope should have deliveries[]');
        $this->assertIsArray($page['deliveries']);
        $this->assertArrayHasKey('pagination', $page);
    }

    public function testWebhookDeliveriesStatsReadOnly(): void
    {
        $proof = $this->makeClient();
        $stats = $proof->webhookDeliveries->stats();
        $this->assertIsArray($stats);
    }

    public function testWaitForCompletionReachesTerminalState(): void
    {
        $proof = $this->makeClient();
        $created = $proof->verifications->create([
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => self::testPhoneIdentifier(),
            'external_user_id' => self::externalUserId(),
            'client_metadata' => self::tagMetadata(['stage' => 'polling']),
        ]);

        // PHP is synchronous — we can't fire the transition from a separate
        // thread. Use the shouldCancel callback as a side-channel: on the
        // second invocation we trigger `testVerify`, which flips the record
        // to verified. By then the next `retrieve()` in the polling loop
        // observes the terminal state. This exercises the real backoff /
        // interval / retrieve machinery rather than short-circuiting on
        // iteration 0.
        $invocations = 0;
        $verified = false;
        $shouldCancel = function () use ($proof, $created, &$invocations, &$verified): bool {
            $invocations++;
            if ($invocations >= 2 && !$verified) {
                $proof->verifications->testVerify($created['id']);
                $verified = true;
            }
            return false;
        };

        $final = $proof->verifications->waitForCompletion(
            $created['id'],
            0.3,  // interval
            20.0, // timeout
            1.5,  // backoff
            1.0,  // max_interval
            0.1,  // jitter
            $shouldCancel,
        );
        $this->assertSame('verified', $final['status']);
        $this->assertGreaterThanOrEqual(2, $invocations, 'polling should have iterated at least twice');
    }

    public function testStreamFirstEvent(): void
    {
        $proof = $this->makeClient();
        $created = $proof->verifications->create([
            'type' => 'phone',
            'channel' => 'sms',
            'identifier' => self::testPhoneIdentifier(),
            'external_user_id' => self::externalUserId(),
            'client_metadata' => self::tagMetadata(['stage' => 'streaming']),
        ]);

        // PHP streaming is synchronous — we can't wait for a real status
        // change to arrive over SSE without blocking. Fire `testVerify`
        // first, then connect; the first event observed proves the
        // SSE/polling transport round-trips a real state at least once.
        $proof->verifications->testVerify($created['id']);

        // 20s deadline — without this the SSE read loop can hang the CI
        // job indefinitely if the first event never arrives. The transport
        // read_timeout in HttpClient::rawStream is what makes this cap
        // actually enforceable at the socket layer.
        $timeoutSeconds = 20;
        $startNs = hrtime(true);
        $deadlineNs = $startNs + $timeoutSeconds * 1_000_000_000;
        $shouldCancel = static fn (): bool => hrtime(true) > $deadlineNs;

        $first = null;
        foreach ($proof->verifications->stream($created['id'], $shouldCancel) as $event) {
            $first = $event;
            break;
        }
        $elapsedSec = (hrtime(true) - $startNs) / 1e9;
        $this->assertNotNull(
            $first,
            sprintf('expected at least one SSE/polling event within %ds; elapsed=%.2fs', $timeoutSeconds, $elapsedSec),
        );
    }
}

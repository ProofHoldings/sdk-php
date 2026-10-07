<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Jwks\Clock;
use ProofHoldings\Jwks\KeySetCache;
use ProofHoldings\Proof;
use ProofHoldings\Resources\Proofs;
use Psr\Http\Message\RequestInterface;

/**
 * Offline verification follows the issuer's key-rotation rules (h-verifier-jwks-go-php).
 *
 * The shared cases in `sdks/shared/fixtures/jwks_rotation/rotation_cases.json` run here through the
 * PUBLIC surface — `new Proof(...)`, `proofs->verifyOffline`, `refreshJWKS`, `clearTombstones` — with
 * the SDK's own credential-free client routed to the case's documents. The same file drives
 * `delegation-verifier` and the other SDKs, so a rule read differently here fails here. Step
 * semantics: the README beside the file.
 *
 * Two fixture names map onto this SDK's own vocabulary, which predates them: `bad_signature` is
 * `invalid` (pinned further by its message, so an `iss` failure cannot pass for it) and
 * `jwks_unavailable` is `status_unavailable` — "could not check".
 *
 * The cases move both clocks together, so they cannot tell them apart; the clock-base tests below
 * set them apart on purpose.
 */
final class JwksRotationFixturesTest extends TestCase
{
    private const MIN = 60;
    private const HOUR = 3600;

    /** @var array<string, mixed> */
    private static array $fixture;

    private FixtureClock $clock;

    /** @var list<string> */
    private array $events = [];

    private ?string $jwks = null;

    private ?string $statusList = null;

    private int $jwksFetches = 0;

    private int $listFetches = 0;

    /** When set, served as the key-set body with HTTP 200 instead of a named document. */
    private ?string $rawJwks = null;

    /** When set, served as the status-list body instead of a named list. */
    private ?string $rawStatusList = null;

    public static function setUpBeforeClass(): void
    {
        self::$fixture = json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/jwks_rotation/rotation_cases.json'),
            true
        );
    }

    protected function setUp(): void
    {
        $this->clock = new FixtureClock();
        KeySetCache::setClockForTests($this->clock);
        $this->events = [];
        KeySetCache::setEventListenerForTests(function (array $event): void {
            $this->events[] = $event['type'];
        });
        $this->jwks = null;
        $this->statusList = null;
        $this->jwksFetches = 0;
        $this->listFetches = 0;
        $this->rawJwks = null;
        $this->rawStatusList = null;
    }

    protected function tearDown(): void
    {
        KeySetCache::setClockForTests(null);
        KeySetCache::setEventListenerForTests(null);
    }

    /** @return list<array<string, mixed>> */
    private static function runnable(): array
    {
        $families = ['proof', 'delegation', 'status-list'];
        $self = json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/jwks_rotation/rotation_cases.json'),
            true
        );

        return array_values(array_filter(
            $self['cases'],
            static fn (array $case): bool => array_diff($case['families'], $families) === []
                && ($case['requires'] ?? []) === []
        ));
    }

    /** @return array<string, array{0: array<string, mixed>}> */
    public static function sharedCases(): array
    {
        $cases = [];
        foreach (self::runnable() as $case) {
            $cases[$case['name']] = [$case];
        }
        return $cases;
    }

    private function context(string $member): mixed
    {
        return self::$fixture['context'][$member];
    }

    private function token(string $name): string
    {
        return self::$fixture['tokens'][$name]['jwt'];
    }

    /** A client whose credential-free transport answers from the fixture. */
    private function proofs(int|float|null $maxJwksAge = null): Proofs
    {
        $proof = $maxJwksAge === null
            ? new Proof('pk_test_fixture', baseUrl: $this->context('issuer_origin'))
            : new Proof('pk_test_fixture', baseUrl: $this->context('issuer_origin'), maxJwksAge: $maxJwksAge);

        // The swapped-in client is this test's own, so its headers prove nothing about the SDK's:
        // `PublicFetchSocketTest` pins the credential-free, branded read of the key set instead.
        $handler = function (RequestInterface $request, array $options) {
            $url = (string) $request->getUri();
            if ($url === $this->context('jwks_uri')) {
                $this->jwksFetches++;
                if ($this->rawJwks !== null) {
                    return Create::promiseFor(new Response(200, ['content-type' => 'application/json'], $this->rawJwks));
                }
                if ($this->jwks === null) {
                    return Create::promiseFor(new Response(503, [], 'unavailable'));
                }
                return Create::promiseFor(new Response(
                    200,
                    ['content-type' => 'application/json'],
                    json_encode(self::$fixture['documents'][$this->jwks])
                ));
            }
            if ($url === $this->context('status_list_uri')) {
                $this->listFetches++;
                if ($this->rawStatusList !== null) {
                    return Create::promiseFor(new Response(200, ['content-type' => 'application/statuslist+jwt'], $this->rawStatusList));
                }
                if ($this->statusList === null) {
                    return Create::promiseFor(new Response(404, [], 'not found'));
                }
                return Create::promiseFor(new Response(
                    200,
                    ['content-type' => 'application/statuslist+jwt'],
                    self::$fixture['status_lists'][$this->statusList]['jwt']
                ));
            }
            $this->fail('unexpected fetch: ' . $url);
        };

        $http = (new \ReflectionProperty(Proofs::class, 'http'))->getValue($proof->proofs);
        (new \ReflectionProperty(HttpClient::class, 'publicClient'))->setValue(
            $http,
            new Client(['handler' => $handler, 'http_errors' => false, 'allow_redirects' => false])
        );

        return $proof->proofs;
    }

    /** The fixture's name for an SDK answer. */
    private static function outcome(array $result): string
    {
        if ($result['valid']) {
            return 'valid';
        }
        return match ($result['reason'] ?? null) {
            'invalid' => 'bad_signature',
            'status_unavailable' => 'jwks_unavailable',
            null => 'missing reason',
            default => $result['reason'],
        };
    }

    /** @return list<string> */
    private static function vanishedKids(Proofs $proofs): array
    {
        $keySet = (new \ReflectionProperty(Proofs::class, 'keySet'))->getValue($proofs);
        return $keySet->diagnostics()['vanishedKids'];
    }

    public function testRunsAMeaningfulShareOfTheSharedCases(): void
    {
        // A filter that skips everything must not pass.
        $names = array_column(self::runnable(), 'name');
        $this->assertGreaterThanOrEqual(30, count($names));
        foreach ([
            'unknown_status_list_kid',
            'tombstone_proof_pull_hint',
            'clear_tombstones_lifts_applied_tombstone',
            'tolerant_mode',
            'max_jwks_age_refetch_failure_over_bound',
        ] as $expected) {
            $this->assertContains($expected, $names);
        }
        $skipped = array_values(array_diff(array_column(self::$fixture['cases'], 'name'), $names));
        $this->assertSame(['tombstone_persists_across_reload'], $skipped);
    }

    #[DataProvider('sharedCases')]
    public function testSharedRotationCase(array $case): void
    {
        $proofs = $this->proofs($case['max_jwks_age_seconds'] ?? $this->context('default_max_jwks_age_seconds'));
        foreach ($case['steps'] as $step) {
            $this->clock->set($step['at'], $step['at']);
            $this->jwks = $step['jwks'];
            $this->statusList = $step['status_list'] ?? null;
            $this->jwksFetches = 0;
            $this->events = [];
            $where = sprintf('%s @ t0%+ds', $case['name'], $step['at'] - $this->context('t0'));

            match ($step['action'] ?? null) {
                null => null,
                'refresh' => $proofs->refreshJWKS(),
                'clear_tombstones' => $proofs->clearTombstones(),
                default => $this->fail($where . ': unsupported action ' . $step['action']),
            };

            $expected = $step['expect'];
            if (isset($step['verify'])) {
                $result = $proofs->verifyOffline($this->token($step['verify']), ($step['status_check'] ?? false) === true);
                $this->assertSame($expected['result'], self::outcome($result), $where . ': ' . ($result['error'] ?? ''));
                if ($expected['result'] === 'bad_signature') {
                    $this->assertSame('signature verification failed', $result['error'], $where);
                }
                if (array_key_exists('pull_hint_handle', $expected)) {
                    $this->assertSame(
                        ['issuer_origin' => $this->context('issuer_origin'), 'handle' => $expected['pull_hint_handle']],
                        $result['pull_hint'] ?? null,
                        $where
                    );
                }
                if (array_key_exists('cache_age_seconds', $expected)) {
                    $this->assertSame($expected['cache_age_seconds'], $result['cache_age_seconds'] ?? null, $where);
                }
            }

            $this->assertSame($expected['fetches'], $this->jwksFetches, $where . ': JWKS fetches');
            foreach ($expected['events'] ?? [] as $eventType) {
                $this->assertContains($eventType, $this->events, $where . ': events');
            }
            if (array_key_exists('vanished_kids', $expected)) {
                $this->assertSame($expected['vanished_kids'], self::vanishedKids($proofs), $where);
            }
        }
    }

    // --- SC-8: the two clocks on different bases ---

    public function testReusesTheKeySetOnTheMonotonicClock(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        $this->clock->set($t0, $t0);
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_active'), false)['valid']);
        $this->clock->set($t0 + self::MIN, $t0 + 11 * self::MIN);
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_active'), false)['valid']);
        $this->assertSame(1, $this->jwksFetches);
        $this->clock->set($t0 + 11 * self::MIN, $t0 + 11 * self::MIN);
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_active'), false)['valid']);
        $this->assertSame(2, $this->jwksFetches);
    }

    public function testMeasuresStalenessOnTheMonotonicClock(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        $this->clock->set($t0, $t0);
        $proofs->verifyOffline($this->token('dlg_active'), false);
        $this->jwks = null;
        $this->clock->set($t0 + 25 * self::HOUR, $t0 + self::HOUR);
        $result = $proofs->verifyOffline($this->token('dlg_active'), false);
        $this->assertSame('jwks_stale', $result['reason']);
        $this->assertSame(25 * self::HOUR, $result['cache_age_seconds']);
    }

    public function testSpacesATombstoneConfirmationOnTheMonotonicClock(): void
    {
        $t0 = $this->context('t0');
        $confirmedThenTombstoned = function (int $monotonic, int $wall) use ($t0): array {
            $proofs = $this->proofs();
            $this->jwks = 'registry_v1';
            foreach ([$t0 - 11 * self::MIN, $t0] as $at) {
                $this->clock->set($at, $at);
                $proofs->verifyOffline($this->token('dlg_retired'), false);
            }
            $this->jwks = 'registry_tombstone_retired_delegation';
            $this->clock->set($t0 + 11 * self::MIN, $t0 + 11 * self::MIN);
            $proofs->verifyOffline($this->token('dlg_retired'), false);
            $this->clock->set($monotonic, $wall);
            $proofs->refreshJWKS();
            return $proofs->verifyOffline($this->token('dlg_retired'), false);
        };

        $this->assertSame('key_revoked', $confirmedThenTombstoned($t0 + 17 * self::MIN, $t0 + 12 * self::MIN)['reason']);
        $this->assertTrue($confirmedThenTombstoned($t0 + 12 * self::MIN, $t0 + 17 * self::MIN)['valid']);
    }

    public function testChecksExpAgainstTheWallClock(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        $this->clock->set($t0, $t0 + 40 * 86_400);
        $this->assertSame('expired', $proofs->verifyOffline($this->token('dlg_active'), false)['reason']);
    }

    public function testChecksTheStatusListExpAgainstTheWallClock(): void
    {
        $t0 = $this->context('t0');
        $run = function (int $wall) use ($t0): array {
            $proofs = $this->proofs();
            $this->jwks = 'registry_v1_status_emergency';
            $this->statusList = 'list_on_emergency_status_key';
            $this->clock->set($t0, $wall);
            return $proofs->verifyOffline($this->token('dlg_active_with_status'));
        };

        $control = $run($t0 + 2 * self::MIN);
        $this->assertTrue($control['valid'], 'positive control: ' . ($control['error'] ?? ''));
        $this->assertTrue($control['revocation_checked']);

        $expired = $run($t0 + 2 * self::HOUR);
        $this->assertSame('status_unavailable', $expired['reason']);
        $this->assertStringContainsString('status list did not verify: Expired token', $expired['error']);
    }

    public function testTheStatusListCacheExpiresOnTheWallClock(): void
    {
        // The list is reused until its own exp — read on the clock every other claim check reads,
        // not on the host's.
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1_status_emergency';
        $this->statusList = 'list_on_emergency_status_key';

        $this->clock->set($t0 + self::MIN, $t0 + self::MIN);
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_active_with_status'))['revocation_checked']);
        $this->clock->set($t0 + self::MIN + 1, $t0 + self::HOUR - 1);
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_active_with_status'))['valid']);
        $this->assertSame(1, $this->listFetches, 'positive control: the list is reused inside its exp');

        $this->clock->set($t0 + self::MIN + 2, $t0 + self::HOUR + 100);
        $result = $proofs->verifyOffline($this->token('dlg_active_with_status'));
        $this->assertSame(2, $this->listFetches);
        $this->assertSame('status_unavailable', $result['reason']);
    }

    public function testAnOversizedKeySetIsAFailedFetch(): void
    {
        $this->rawJwks = '{"keys":[],"padding":"' . str_repeat('x', 524288) . '"}';
        $this->clock->set($this->context('t0'), $this->context('t0'));

        $result = $this->proofs()->verifyOffline($this->token('dlg_active'), false);

        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertStringContainsString('too large', $result['error']);
    }

    public function testTheStatusListIsHeldToItsKeyPolicy(): void
    {
        // The list is a token like any other: its signing key must be a live status-list key, its
        // iat inside that key's window and its lifetime within the key's bound. Each refusal leaves
        // the proof `status_unavailable` — never valid on a list its key could not vouch for.
        $t0 = $this->context('t0');
        $day = 86_400;
        $keys = [];
        foreach (['prf', 'dlg', 'sts', 'sts-old'] as $kid) {
            $keys[$kid] = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);
        }
        $jwk = function (string $kid, string $purpose, int $lifetime) use ($keys, $t0, $day): array {
            $details = openssl_pkey_get_details($keys[$kid]);
            $coordinate = static fn (string $raw): string => rtrim(strtr(base64_encode(str_pad($raw, 32, "\0", STR_PAD_LEFT)), '+/', '-_'), '=');
            return [
                'kty' => 'EC', 'crv' => 'P-256', 'alg' => 'ES256', 'use' => 'sig', 'kid' => $kid,
                'x' => $coordinate($details['ec']['x']), 'y' => $coordinate($details['ec']['y']),
                'proof_purpose' => $purpose, 'not_before' => $t0 - 30 * $day, 'not_after' => $t0 + 30 * $day,
                'max_token_lifetime' => $lifetime,
            ];
        };
        $document = function (bool $tombstoned) use ($jwk, $t0, $day): string {
            $served = [$jwk('prf', 'proof', 365 * $day), $jwk('dlg', 'delegation', 365 * $day), $jwk('sts', 'status-list', $day)];
            $tombstones = [];
            if ($tombstoned) {
                $tombstones[] = ['kid' => 'sts-old', 'revoked_at' => $t0 - $day];
            } else {
                $served[] = $jwk('sts-old', 'status-list', $day);
            }
            return json_encode(['keys' => $served, 'proof_tombstones' => $tombstones]);
        };
        $list = fn (string $kid, int $iat, int $exp): string => \Firebase\JWT\JWT::encode(
            [
                'sub' => $this->context('status_list_uri'), 'iat' => $iat, 'exp' => $exp,
                'status_list' => ['bits' => 2, 'lst' => rtrim(strtr(base64_encode((string) gzcompress("\0")), '+/', '-_'), '=')],
            ],
            $keys[$kid],
            'ES256',
            $kid,
            ['typ' => 'statuslist+jwt']
        );
        $proof = \Firebase\JWT\JWT::encode(
            [
                'iss' => 'proof.holdings', 'sub' => 'ph_ctl_x', 'proof_id' => 'ph_ctl_x', 'iat' => $t0 - 10 * self::MIN, 'exp' => $t0 + 3600,
                'type' => 'domain', 'channel' => 'dns', 'verified_at' => '2026-12-31T23:59:00.000Z',
                'status' => ['status_list' => ['idx' => 0, 'uri' => $this->context('status_list_uri')]],
            ],
            $keys['prf'],
            'ES256',
            'prf'
        );
        $run = function (string $statusList, bool $tombstoned) use ($document, $proof, $t0): array {
            $proofs = $this->proofs();
            $this->rawJwks = $document($tombstoned);
            $this->rawStatusList = $statusList;
            // Two fetches six minutes apart: what confirms the keys and applies a tombstone.
            $this->clock->set($t0 - 6 * self::MIN, $t0 - 6 * self::MIN);
            $priming = $proofs->verifyOffline($proof, false);
            $this->assertTrue($priming['valid'], 'priming: ' . ($priming['error'] ?? ''));
            $this->clock->set($t0, $t0);
            $proofs->refreshJWKS();
            return $proofs->verifyOffline($proof);
        };

        $control = $run($list('sts', $t0 - 30, $t0 + 3600), false);
        $this->assertTrue($control['valid'], 'positive control: ' . ($control['error'] ?? ''));
        $this->assertTrue($control['revocation_checked']);

        foreach ([
            'a delegation key' => [$list('dlg', $t0 - 30, $t0 + 3600), false, 'is not a status-list key'],
            'a revoked status-list key' => [$list('sts-old', $t0 - 30, $t0 + 3600), true, 'revoked signing key'],
            "an iat before the key's window" => [$list('sts', $t0 - 31 * $day, $t0 + 3600), false, "outside the signing key's window"],
            "a lifetime beyond the key's bound" => [$list('sts', $t0 - 30, $t0 + 2 * $day), false, 'max_token_lifetime'],
        ] as $name => [$statusList, $tombstoned, $want]) {
            $result = $run($statusList, $tombstoned);
            $this->assertFalse($result['valid'], $name);
            $this->assertSame('status_unavailable', $result['reason'], $name);
            $this->assertStringContainsString($want, $result['error'], $name);
        }
    }

    public function testAKeyOffItsCurveIsNoCandidate(): void
    {
        // php-jwt imports an EC point without checking it lies on the curve; the other SDKs' imports
        // refuse it, so the kid is unknown there — and must be here, not a "bad signature".
        $document = self::$fixture['documents']['registry_v1'];
        $onePoint = rtrim(strtr(base64_encode(str_repeat("\0", 31) . "\1"), '+/', '-_'), '=');
        foreach ($document['keys'] as $index => $jwk) {
            if ($jwk['kid'] === 'dlg-2026-12') {
                $document['keys'][$index]['x'] = $onePoint;
                $document['keys'][$index]['y'] = $onePoint;
            }
        }
        $this->rawJwks = json_encode($document);
        $this->clock->set($this->context('t0'), $this->context('t0'));

        $result = $this->proofs()->verifyOffline($this->token('dlg_active'), false);

        $this->assertSame('unknown_key', $result['reason'], $result['error'] ?? '');
    }

    public function testAnAbsentWindowMemberIsNotZero(): void
    {
        // PHP's loose `null == 0` must not make a key served without `not_before` and one served
        // with `not_before: 0` the same description — the second replaces the first, unconfirmed.
        $absent = json_decode('{"keys":[{"kid":"k","kty":"EC"}]}');
        $zero = json_decode('{"keys":[{"kid":"k","kty":"EC","not_before":0}]}');
        $first = \ProofHoldings\Jwks\KeySet::mergeFetchedDocument(
            new \ProofHoldings\Jwks\KeySetState(),
            $absent,
            new \ProofHoldings\Jwks\ClockReading(0.0, 0.0)
        )['state'];
        $sixMinutes = new \ProofHoldings\Jwks\ClockReading(360_000.0, 360_000.0);

        $same = \ProofHoldings\Jwks\KeySet::mergeFetchedDocument($first, $absent, $sixMinutes)['state'];
        $this->assertTrue($same->keys['k']->confirmed, 'positive control: the same description confirms');

        $changed = \ProofHoldings\Jwks\KeySet::mergeFetchedDocument($first, $zero, $sixMinutes)['state'];
        $this->assertFalse($changed->keys['k']->confirmed);
        $this->assertSame(0, $changed->keys['k']->notBefore);
    }

    // --- SC-1, SC-3, SC-4 ---

    /** @return array<string, array{0: mixed}> */
    public static function unusableMaxJwksAges(): array
    {
        return [
            'over the cap' => [25 * 86_400 + 1],
            'zero' => [0],
            'negative' => [-1],
            'NaN' => [NAN],
            'infinite' => [INF],
        ];
    }

    #[DataProvider('unusableMaxJwksAges')]
    public function testRejectsAnUnusableMaxJwksAgeAtConstruction(mixed $bad): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('maxJwksAge');
        new Proof('pk_test_x', maxJwksAge: $bad);
    }

    public function testAcceptsTheCapAndTheDefault(): void
    {
        $atCap = new Proof('pk_test_x', maxJwksAge: 25 * 86_400);
        $default = new Proof('pk_test_x');
        $maxAgeMs = static fn (Proof $proof): float => (new \ReflectionProperty(KeySetCache::class, 'maxJwksAgeMs'))
            ->getValue((new \ReflectionProperty(Proofs::class, 'keySet'))->getValue($proof->proofs));
        $this->assertSame(25 * 86_400 * 1000.0, (float) $maxAgeMs($atCap));
        $this->assertSame(24 * 3600 * 1000.0, (float) $maxAgeMs($default));
    }

    public function testExposesVerifiedAt(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        $this->clock->set($t0, $t0);
        $token = $this->token('prf_active');
        $segment = explode('.', $token)[1];
        $claims = json_decode((string) base64_decode(strtr($segment, '-_', '+/')), true);
        $this->assertIsString($claims['verified_at']);

        $result = $proofs->verifyOffline($token, false);

        $this->assertTrue($result['valid'], $result['error'] ?? '');
        $this->assertSame($claims['verified_at'], $result['payload']['verified_at']);
    }

    public function testRefreshKeepsAnAppliedTombstoneAndOnlyClearLiftsIt(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        foreach ([$t0 - 11 * self::MIN, $t0] as $at) {
            $this->clock->set($at, $at);
            $proofs->verifyOffline($this->token('dlg_retired'), false);
        }
        $this->jwks = 'registry_tombstone_retired_delegation';
        foreach ([$t0 + 11 * self::MIN, $t0 + 16 * self::MIN] as $at) {
            $this->clock->set($at, $at);
            $proofs->refreshJWKS();
            $proofs->verifyOffline($this->token('dlg_retired'), false);
        }
        $this->assertSame('key_revoked', $proofs->verifyOffline($this->token('dlg_retired'), false)['reason']);

        $this->jwks = 'registry_v1';
        $this->clock->set($t0 + 17 * self::MIN, $t0 + 17 * self::MIN);
        $proofs->refreshJWKS();
        $this->assertSame('key_revoked', $proofs->verifyOffline($this->token('dlg_retired'), false)['reason']);
        $this->assertSame(5, $this->jwksFetches);

        $proofs->clearTombstones();
        $this->assertTrue($proofs->verifyOffline($this->token('dlg_retired'), false)['valid']);
    }

    public function testLeavesThePhpJwtGlobalsAsItFoundThem(): void
    {
        $t0 = $this->context('t0');
        $proofs = $this->proofs();
        $this->jwks = 'registry_v1';
        $this->clock->set($t0, $t0 + 40 * 86_400);
        \Firebase\JWT\JWT::$leeway = 0;
        \Firebase\JWT\JWT::$timestamp = null;

        $this->assertSame('expired', $proofs->verifyOffline($this->token('dlg_active'), false)['reason']);

        $this->assertSame(0, \Firebase\JWT\JWT::$leeway);
        $this->assertNull(\Firebase\JWT\JWT::$timestamp);
    }

    public function testReadmeStatesThatTombstonesAreKeptInMemoryOnly(): void
    {
        $readme = (string) file_get_contents(__DIR__ . '/../README.md');
        $sections = array_values(array_filter(
            preg_split('/^### /m', $readme),
            static fn (string $part): bool => str_starts_with($part, 'Signing-key rotation')
        ));
        $this->assertNotEmpty($sections, 'README heading "### Signing-key rotation"');
        $this->assertStringContainsString('in memory', $sections[0]);
        $this->assertStringContainsString('not persisted', $sections[0]);
        $this->assertStringContainsString('clearTombstones()', $sections[0]);
    }
}

/** Both clocks, set by hand to a case's `at`. */
final class FixtureClock implements Clock
{
    private float $monotonicSeconds = 0.0;

    private float $wallSeconds = 0.0;

    public function set(int|float $monotonicSeconds, int|float $wallSeconds): void
    {
        $this->monotonicSeconds = (float) $monotonicSeconds;
        $this->wallSeconds = (float) $wallSeconds;
    }

    public function monotonicMs(): float
    {
        return $this->monotonicSeconds * 1000;
    }

    public function wallMs(): float
    {
        return $this->wallSeconds * 1000;
    }
}

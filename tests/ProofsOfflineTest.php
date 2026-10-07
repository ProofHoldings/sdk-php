<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Jwks\Clock;
use ProofHoldings\Jwks\KeySetCache;
use ProofHoldings\Resources\Proofs;
use Psr\Http\Message\RequestInterface;

/**
 * Offline verification reads the token's status list (h-sdk-offline-revocation).
 *
 * Before this task `verifyOffline` decoded the token against the key set and stopped — no issuer
 * pin, no clock tolerance, and nothing at all about revocation, so a REVOKED proof verified as
 * valid (measured against production on 2026-09-20) and so did a token from ANY issuer whose key
 * happened to sit in the fetched key set.
 *
 * The packed list comes from `sdks/shared/fixtures/status_list_slots.json`, the same fixture the
 * issuer's drift suite and the other three SDKs read.
 *
 * Which status-list origins may be fetched comes from `sdks/shared/fixtures/status_list_origin.json`,
 * shared with the other three SDKs and `delegation-verifier`.
 */
class ProofsOfflineTest extends TestCase
{
    private const KID = 'es256-kid-1';
    private const BASE_URL = 'https://api.example.test';

    /** @var array<string, mixed> */
    private static array $fixture;

    private \OpenSSLAsymmetricKey $privateKey;
    private \OpenSSLAsymmetricKey $foreignKey;
    /** The kid the fixture server currently publishes — changed by a rotation case. */
    private string $publishedKid = self::KID;
    /** @var list<string> */
    private array $requestedUrls = [];
    private string $statusListReply = 'token';
    private string $jwksReply = 'keys';
    /** @var array<string, mixed> */
    private array $statusListOverrides = [];

    /**
     * The key set's monotonic clock, moved by hand; the wall clock stays real because the tokens
     * here are minted against `time()`.
     */
    private Clock&\stdClass $clock;

    public static function setUpBeforeClass(): void
    {
        self::$fixture = json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/status_list_slots.json'),
            true
        );
    }

    protected function setUp(): void
    {
        $this->privateKey = $this->generateKey();
        $this->foreignKey = $this->generateKey();
        $this->publishedKid = self::KID;
        $this->requestedUrls = [];
        $this->statusListReply = 'token';
        $this->jwksReply = 'keys';
        $this->statusListOverrides = [];
        $this->clock = new class () extends \stdClass implements Clock {
            public float $offsetMs = 0.0;

            public function monotonicMs(): float
            {
                return $this->offsetMs;
            }

            public function wallMs(): float
            {
                return microtime(true) * 1000;
            }
        };
        KeySetCache::setClockForTests($this->clock);
    }

    protected function tearDown(): void
    {
        KeySetCache::setClockForTests(null);
    }

    private function generateKey(): \OpenSSLAsymmetricKey
    {
        $key = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);
        self::assertInstanceOf(\OpenSSLAsymmetricKey::class, $key);

        return $key;
    }

    private function statusListUrl(): string
    {
        return self::BASE_URL . '/api/v1/proofs/status-list';
    }

    /** The JWKS document the fixture server publishes for the signing key. */
    private function jwks(): array
    {
        $details = openssl_pkey_get_details($this->privateKey);

        return ['keys' => [[
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $this->b64url($this->padCoordinate($details['ec']['x'])),
            'y' => $this->b64url($this->padCoordinate($details['ec']['y'])),
            'use' => 'sig',
            'alg' => 'ES256',
            'kid' => $this->publishedKid,
        ]]];
    }

    private function b64url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    /**
     * Left-pad a P-256 coordinate to the full 32 octets RFC 7518 §6.2.1.2 requires.
     *
     * `openssl_pkey_get_details` returns the raw integer with leading zero bytes stripped, which
     * happens for roughly one key in a hundred (measured: 19 of 2000). A short coordinate makes
     * the published JWK invalid, `JWT::decode` throws `DomainException: OpenSSL unable to
     * validate key`, and the case reads it as `reason: invalid` — so the suite failed for about
     * one run in four, on a different case each time, for a reason having nothing to do with the
     * behaviour under test.
     */
    private function padCoordinate(string $raw): string
    {
        return str_pad($raw, 32, "\0", STR_PAD_LEFT);
    }

    /** A proof token as the issuer mints it; `$slot === null` means it carries no slot. */
    private function mintProof(
        ?array $slot = ['idx' => 1],
        ?\OpenSSLAsymmetricKey $key = null,
        string $issuer = 'proof.holdings',
        ?int $iat = null
    ): string {
        $now = time();
        $claims = [
            'iss' => $issuer,
            'sub' => 'ph_ctl_0123456789abcdef0123456789abcdef',
            'iat' => $iat ?? $now - 60,
            'exp' => $now + 3600,
            'type' => 'phone',
            'channel' => 'whatsapp',
            'identifier_hash' => 'abc123',
            'verified_at' => '2026-09-21T10:00:00.000Z',
            'user_id' => 'usr_1',
        ];
        if ($slot !== null) {
            $claims['status'] = ['status_list' => [
                'idx' => $slot['idx'],
                'uri' => $slot['uri'] ?? $this->statusListUrl(),
            ]];
        }

        return JWT::encode($claims, $key ?? $this->privateKey, 'ES256', $this->publishedKid);
    }

    /** The signed status list, as `GET /api/v1/proofs/status-list` serves it. */
    private function mintStatusList(): string
    {
        $o = $this->statusListOverrides;
        $claims = [
            'sub' => $o['sub'] ?? $this->statusListUrl(),
            'iat' => time(),
            'ttl' => 55,
            'status_list' => [
                'bits' => self::$fixture['bits'],
                'lst' => self::$fixture['lst'],
            ],
        ];
        if (($o['omit_exp'] ?? false) !== true) {
            $claims['exp'] = time() + ($o['expires_in'] ?? 300);
        }

        $head = ['typ' => $o['typ'] ?? 'statuslist+jwt'];

        return JWT::encode($claims, $o['key'] ?? $this->privateKey, 'ES256', $this->publishedKid, $head);
    }

    /**
     * A Proofs resource whose transport answers from this fixture instead of the network.
     *
     * The public-artifact client inside HttpClient is swapped by reflection — the pattern the
     * rest of this SDK's suite uses — so both reads offline verification makes (the key set and
     * the status list) are routed here and can be asserted on.
     */
    private function proofs(): Proofs
    {
        $handler = function (RequestInterface $request, array $options) {
            $url = (string) $request->getUri();
            $this->requestedUrls[] = $url;

            if (str_ends_with($url, '/.well-known/jwks.json')) {
                return match ($this->jwksReply) {
                    'error' => Create::promiseFor(new Response(503, [], 'unavailable')),
                    // `{"keys": []}` is what an issuer can serve mid-rotation, and an
                    // unrecognised curve is one malformed entry. `JWK::parseKeySet` throws
                    // LogicException descendants for both.
                    'empty' => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/json'],
                        json_encode(['keys' => []])
                    )),
                    // An HTTP 200 that is not a key set at all.
                    'not_a_key_set' => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/json'],
                        json_encode(['error' => 'maintenance'])
                    )),
                    'bad_curve' => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/json'],
                        json_encode(['keys' => [[
                            'kty' => 'EC', 'crv' => 'P-999', 'x' => 'AA', 'y' => 'AA',
                            'use' => 'sig', 'alg' => 'ES256', 'kid' => self::KID,
                        ]]])
                    )),
                    default => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/json'],
                        json_encode($this->jwks())
                    )),
                };
            }

            if (str_ends_with($url, '/api/v1/proofs/status-list')) {
                return match ($this->statusListReply) {
                    'hangup' => Create::rejectionFor(
                        new ConnectException('connection refused', new Request('GET', $url))
                    ),
                    'error' => Create::promiseFor(new Response(503, [], 'unavailable')),
                    'oversized' => Create::promiseFor(new Response(
                        200,
                        ['content-length' => (string) (600 * 1024)],
                        str_repeat('x', 600 * 1024)
                    )),
                    // Same body, no Content-Length — the declared-length check cannot see this
                    // one, so it pins the read-length ceiling on its own.
                    'oversized_undeclared' => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/statuslist+jwt'],
                        str_repeat('x', 600 * 1024)
                    )),
                    default => Create::promiseFor(new Response(
                        200,
                        ['content-type' => 'application/statuslist+jwt'],
                        $this->mintStatusList()
                    )),
                };
            }

            return Create::promiseFor(new Response(404, [], 'not found'));
        };

        $http = new HttpClient('pk_test_123', self::BASE_URL, 5.0, 0);
        $property = new \ReflectionProperty(HttpClient::class, 'publicClient');
        $property->setValue($http, new Client(['handler' => $handler, 'http_errors' => false]));

        return new Proofs($http);
    }

    private function statusListRequests(): int
    {
        return count(array_filter(
            $this->requestedUrls,
            static fn (string $url): bool => str_contains($url, 'status-list')
        ));
    }

    // --- revocation is checked by default ---

    public function testRefusesRevokedSlot(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 1]));

        $this->assertFalse($result['valid']);
        $this->assertSame('revoked', $result['reason']);
        $this->assertTrue($result['revocation_checked']);
    }

    public function testRefusesSuspendedSlot(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 2]));

        $this->assertFalse($result['valid']);
        $this->assertSame('suspended', $result['reason']);
        $this->assertTrue($result['revocation_checked']);
    }

    public function testAcceptsClearSlot(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertArrayNotHasKey('error', $result);
        $this->assertTrue($result['valid']);
        $this->assertTrue($result['revocation_checked']);
        $this->assertSame('usr_1', $result['payload']['user_id']);
    }

    public function testRefusesApplicationReservedSlot(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 3]));

        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertFalse($result['revocation_checked']);
    }

    public function testRefusesSlotBeyondTheList(): void
    {
        $result = $this->proofs()->verifyOffline(
            $this->mintProof(['idx' => self::$fixture['beyond_end_index']])
        );

        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
    }

    // --- a token with no slot ---

    public function testTokenWithoutSlotStaysValidAndAsksForNoList(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(null));

        // The issuer answers valid:true for a token it cannot resolve in the registry
        // (src/controllers/proofs.ts) — a client refusing it would be stricter than the server.
        $this->assertTrue($result['valid']);
        $this->assertFalse($result['revocation_checked']);
        $this->assertArrayNotHasKey('reason', $result);
        $this->assertSame(0, $this->statusListRequests());
    }

    // --- an unreadable list fails closed ---

    public function testConnectionFailureFailsClosed(): void
    {
        $this->statusListReply = 'hangup';

        $result = $this->proofs()->verifyOffline($this->mintProof());

        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertFalse($result['revocation_checked']);
    }

    public function testHttpErrorFailsClosed(): void
    {
        $this->statusListReply = 'error';

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testOversizedBodyFailsClosed(): void
    {
        $this->statusListReply = 'oversized';

        // The MESSAGE is asserted, not just the reason: a 600 KiB run of "x" also fails to parse
        // as a JWT, so the reason alone stays green with the size ceiling deleted.
        $result = $this->proofs()->verifyOffline($this->mintProof());

        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertStringContainsString('too large', $result['error']);
    }

    public function testForeignStatusListOriginIsNeverContacted(): void
    {
        $token = $this->mintProof([
            'idx' => 1,
            'uri' => 'https://evil.example.net/api/v1/proofs/status-list',
        ]);

        $result = $this->proofs()->verifyOffline($token);

        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertStringContainsString('origin', $result['error']);
        $this->assertSame([], array_filter(
            $this->requestedUrls,
            static fn (string $url): bool => str_contains($url, 'evil.example.net')
        ));
    }

    // --- what makes a status list acceptable ---

    public function testRefusesListWithoutStatusListTyp(): void
    {
        $this->statusListOverrides = ['typ' => 'JWT'];

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testRefusesListWhoseSubIsAnotherUri(): void
    {
        $this->statusListOverrides = ['sub' => self::BASE_URL . '/api/v1/proofs/other-list'];

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testRefusesExpiredList(): void
    {
        $this->statusListOverrides = ['expires_in' => -3600];

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testRefusesListWithNoExp(): void
    {
        $this->statusListOverrides = ['omit_exp' => true];

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testRefusesListSignedByUnpublishedKey(): void
    {
        $this->statusListOverrides = ['key' => $this->foreignKey];

        $this->assertSame('status_unavailable', $this->proofs()->verifyOffline($this->mintProof())['reason']);
    }

    public function testAcceptsListCarryingNoIssuerClaim(): void
    {
        // The issuer signs its list without an `iss` claim (src/services/statusList.ts), so
        // requiring one would refuse every real list.
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertTrue($result['valid']);
        $this->assertTrue($result['revocation_checked']);
    }

    // --- the opt-out ---

    public function testOptOutSkipsTheList(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 1]), false);

        $this->assertTrue($result['valid']);
        $this->assertFalse($result['revocation_checked']);
        $this->assertSame(0, $this->statusListRequests());
    }

    // --- PHP-only: the issuer pin and the clock tolerance the other three SDKs already had ---

    public function testRefusesATokenFromAnotherIssuer(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 0], null, 'evil.example.net'));

        $this->assertFalse($result['valid']);
        $this->assertSame('invalid', $result['reason']);
        $this->assertFalse($result['revocation_checked']);
    }

    public function testAcceptsATokenIssuedAFewSecondsIntoTheFuture(): void
    {
        // A freshly minted proof reaches a verifier whose clock trails the issuer's; the other
        // three SDKs tolerate 5 seconds of that and this one used to tolerate none.
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 0], null, 'proof.holdings', time() + 3));

        $this->assertTrue($result['valid'], $result['error'] ?? '');
    }

    public function testRefusesATokenSignedByAForeignKey(): void
    {
        $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => 0], $this->foreignKey));

        $this->assertFalse($result['valid']);
        $this->assertSame('invalid', $result['reason']);
    }

    // --- the shared slot fixture ---

    public function testReadsEverySlotOfTheSharedFixture(): void
    {
        $expected = [
            'valid' => [true, null],
            'revoked' => [false, 'revoked'],
            'suspended' => [false, 'suspended'],
            'unavailable' => [false, 'status_unavailable'],
        ];

        foreach (self::$fixture['slots'] as $index => $name) {
            $this->setUp();
            $result = $this->proofs()->verifyOffline($this->mintProof(['idx' => (int) $index]));

            $this->assertSame(
                [$index, $expected[$name][0], $expected[$name][1]],
                [$index, $result['valid'], $result['reason'] ?? null]
            );
        }
    }

    // --- the caches ---

    public function testStatusListIsReadOnceAcrossTwoVerifications(): void
    {
        $proofs = $this->proofs();

        $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        $proofs->verifyOffline($this->mintProof(['idx' => 1]));

        $this->assertSame(1, $this->statusListRequests());
    }

    public function testStatusListIsRefetchedOnceItsWindowPasses(): void
    {
        // `expires_at` is what keeps the product's 60-second revocation-visibility promise on the
        // client side: past it the list must be read again, or a revocation stays invisible for
        // the token's full `exp`.
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $cache = new \ReflectionProperty(Proofs::class, 'statusListCache');
        $held = $cache->getValue($proofs);
        $held['expires_at'] = time() - 1;
        $cache->setValue($proofs, $held);

        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertSame(2, $this->statusListRequests());
    }

    public function testRefreshJwksDropsTheStatusListToo(): void
    {
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $proofs->refreshJWKS();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertSame(2, $this->statusListRequests());
    }

    public function testRecoversFromAKeyRotationWithOneRefetch(): void
    {
        // Regression guard: holding the key set forever made every proof minted under a rotated
        // key read as `invalid` — a forgery's answer — for the life of the process.
        $proofs = $this->proofs();
        $this->assertTrue($proofs->verifyOffline($this->mintProof(['idx' => 0]))['valid']);
        $before = $this->jwksRequests();

        $this->publishedKid = 'es256-kid-2';
        $result = $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertTrue($result['valid'], $result['error'] ?? '');
        $this->assertSame($before + 1, $this->jwksRequests());
    }

    public function testAnUnknownKidInsideTheCooldownDoesNotRefetch(): void
    {
        // The other half of the same rule: a rotated key and an attacker-supplied junk kid look
        // identical here, so the healing refetch is rate-limited rather than free. The first
        // unknown kid buys one refetch and answers `unknown_key`; the next one inside the cooldown
        // answers "could not check" — it may have been published since — without a request.
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        $before = $this->jwksRequests();

        $this->publishedKid = 'junk-kid-1';
        $first = $this->mintProof(['idx' => 0]);
        $this->publishedKid = 'junk-kid-2';
        $second = $this->mintProof(['idx' => 0]);
        $this->publishedKid = self::KID;

        $this->assertSame('unknown_key', $proofs->verifyOffline($first)['reason']);
        $this->assertSame('status_unavailable', $proofs->verifyOffline($second)['reason']);
        $this->assertSame($before + 1, $this->jwksRequests());
    }

    // --- the remaining ceiling half, the origin rule, and an unreadable key set ---

    public function testOversizedBodyWithoutContentLengthFailsClosed(): void
    {
        $this->statusListReply = 'oversized_undeclared';

        $result = $this->proofs()->verifyOffline($this->mintProof());

        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertStringContainsString('too large', $result['error']);
    }

    public function testStatusListUriIsAcceptedRegardlessOfDefaultPortOrHostCase(): void
    {
        // One rule across the four SDKs: the host is compared case-insensitively and an explicit
        // default port is the same origin as none. JS gets this from `URL.origin`; the other
        // three normalize by hand.
        foreach ([
            'https://API.Example.Test/api/v1/proofs/status-list',
            'https://api.example.test:443/api/v1/proofs/status-list',
        ] as $uri) {
            $this->setUp();
            $this->proofs()->verifyOffline($this->mintProof(['idx' => 1, 'uri' => $uri]));

            // The assertion is that the ORIGIN rule let the fetch happen. It deliberately stops
            // there: the list's own `sub` is compared to the uri byte-for-byte after that, and
            // these two spellings do not match the `sub` the issuer signs, so the verification
            // still ends in `status_unavailable` — for a different reason than the one under test.
            $this->assertSame(1, $this->statusListRequests(), $uri);
        }
    }

    public function testAnUnreadableKeySetIsNotReportedAsAForgery(): void
    {
        $http = new HttpClient('pk_test_123', self::BASE_URL, 5.0, 0);
        $property = new \ReflectionProperty(HttpClient::class, 'publicClient');
        $property->setValue($http, new Client([
            'handler' => static fn (RequestInterface $r, array $o) => Create::promiseFor(
                new Response(503, [], 'nope')
            ),
            'http_errors' => false,
        ]));

        $result = (new Proofs($http))->verifyOffline($this->mintProof());

        // "I could not check" is not "this proof is bad": `invalid` is what a forged signature
        // answers, and a key-set outage must not be indistinguishable from one.
        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
    }

    /** Move the key set's monotonic clock forward: its age and every cooldown read it. */
    private function ageTheKeySet(int $seconds): void
    {
        $this->clock->offsetMs += $seconds * 1000;
    }

    private function jwksRequests(): int
    {
        return count(array_filter(
            $this->requestedUrls,
            static fn (string $url): bool => str_contains($url, 'jwks.json')
        ));
    }

    // --- a key set that cannot be parsed must ANSWER, not throw ---

    /**
     * @return list<array{0: string}>
     */
    public static function unparseableKeySets(): array
    {
        return [['empty'], ['bad_curve']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unparseableKeySets')]
    public function testAnUnparseableKeySetAnswersInsteadOfThrowing(string $reply): void
    {
        // `JWK::parseKeySet` used to throw InvalidArgumentException for an empty set and
        // DomainException for an unrecognised curve — both LogicException descendants, which
        // escaped verifyOffline as a 500. Both are well-formed documents that publish no usable
        // key for this kid, and a refetch was made for it: `unknown_key`, never an exception.
        $this->jwksReply = $reply;

        $result = $this->proofs()->verifyOffline($this->mintProof());

        $this->assertFalse($result['valid']);
        $this->assertSame('unknown_key', $result['reason']);
    }

    public function testTheHeldKeySetIsRefetchedOnceItIsOldEnough(): void
    {
        // The unknown-kid refetch heals a key being ADDED. Nothing heals one being REMOVED, so a
        // withdrawn — possibly compromised — key would stay trusted for the life of the process.
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        $before = $this->jwksRequests();

        $this->ageTheKeySet(601);

        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertGreaterThan($before, $this->jwksRequests());
    }

    public function testAFailedFetchOpensTheCooldownWindowToo(): void
    {
        // Measuring the cooldown from the last SUCCESS leaves it closed exactly when the endpoint
        // is already failing: every unknown kid would retry straight into an endpoint that just
        // answered an error.
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->jwksReply = 'error';
        $this->publishedKid = 'es256-kid-2';
        $this->ageTheKeySet(601);

        $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        $afterFailure = $this->jwksRequests();

        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertSame($afterFailure, $this->jwksRequests());
    }

    public function testAResponseThatIsNotAKeySetDoesNotRenewTheHeldSetsFreshness(): void
    {
        // An HTTP 200 carrying something that is not a key set must count as a FAILED fetch. If it
        // were merged as an empty document it would renew the set's age, and the TTL — what heals
        // a REMOVED key — would stop running.
        $proofs = $this->proofs();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        // A second fetch 6 minutes later confirms the key, so the cache may answer for it while a
        // refetch fails.
        $this->ageTheKeySet(360);
        $proofs->refreshJWKS();
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->ageTheKeySet(601);
        $this->jwksReply = 'not_a_key_set';
        $result = $proofs->verifyOffline($this->mintProof(['idx' => 0]));
        $this->assertTrue($result['valid'], 'the confirmed key answers from the cache: ' . ($result['error'] ?? ''));
        $afterTheBadAnswer = $this->jwksRequests();

        // Past the failed-refetch cooldown but well inside a TTL: if the bad answer had renewed
        // freshness, the set would still count as young and no further fetch would happen.
        $this->ageTheKeySet(31);
        $this->jwksReply = 'keys';
        $proofs->verifyOffline($this->mintProof(['idx' => 0]));

        $this->assertSame($afterTheBadAnswer + 1, $this->jwksRequests());
    }

    /** @return array<string, array{array{name: string, uri: string, sdk: string, uri_origin?: string}}> */
    public static function statusListOriginCases(): array
    {
        $cases = [];
        foreach (self::statusListOriginFixture()['cases'] as $case) {
            $cases[$case['name']] = [$case];
        }

        return $cases;
    }

    /** @return array{configured_origin: string, cases: list<array<string, string>>} */
    private static function statusListOriginFixture(): array
    {
        return json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/status_list_origin.json'),
            true
        );
    }

    /** @param array{name: string, uri: string, sdk: string, uri_origin?: string} $case */
    #[\PHPUnit\Framework\Attributes\DataProvider('statusListOriginCases')]
    public function testTheSharedStatusListOriginFixture(array $case): void
    {
        $configured = self::statusListOriginFixture()['configured_origin'];
        $jwksUrl = $configured . '/.well-known/jwks.json';
        $requested = [];
        $http = new HttpClient('pk_test_123', $configured, 5.0, 0);
        $property = new \ReflectionProperty(HttpClient::class, 'publicClient');
        $property->setValue($http, new Client([
            'handler' => function (RequestInterface $request, array $options) use (&$requested, $jwksUrl) {
                $url = (string) $request->getUri();
                $requested[] = $url;
                if ($url === $jwksUrl) {
                    return Create::promiseFor(
                        new Response(200, ['Content-Type' => 'application/json'], (string) json_encode($this->jwks()))
                    );
                }

                return Create::promiseFor(new Response(404, [], 'not found'));
            },
            'http_errors' => false,
        ]));

        $result = (new Proofs($http))->verifyOffline($this->mintProof(['idx' => 1, 'uri' => $case['uri']]));
        $listPath = parse_url($case['uri'], PHP_URL_PATH);
        $listRequests = array_values(array_filter(
            $requested,
            static fn (string $url): bool => parse_url($url, PHP_URL_PATH) === $listPath
        ));

        // The key set was read and the signature passed, so the origin rule decided the rest.
        $this->assertContains($jwksUrl, $requested);
        $this->assertArrayHasKey('payload', $result);
        $this->assertFalse($result['valid']);
        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertFalse($result['revocation_checked']);

        if ($case['sdk'] === 'accepted') {
            // The list itself is a 404 here: what is pinned is that the origin rule let the request go.
            $this->assertCount(1, $listRequests);
            $this->assertStringStartsWith($configured . '/', $listRequests[0]);
            $this->assertStringNotContainsString('configured issuer origin', $result['error']);

            return;
        }
        $this->assertSame([], $listRequests);
        $this->assertSame(
            sprintf('status list uri origin %s is not the configured issuer origin %s', $case['uri_origin'], $configured),
            $result['error']
        );
    }
}

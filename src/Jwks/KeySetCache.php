<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

use ProofHoldings\HttpClient;

/**
 * The issuer's key set as offline verification holds it — the I/O half around `KeySet`, which
 * owns the rules. Mirrors `packages/delegation-verifier/src/jwks.ts`, minus persistence: this SDK
 * keeps tombstones in memory only.
 *
 * - A fetched set is reused for 10 minutes. An unknown `kid` triggers one refetch per 30 s
 *   cooldown; inside that cooldown it answers "could not check" — the kid may have been published
 *   since — never "no such key".
 * - A fetch is MERGED into what the verifier knows, never swapped in.
 * - A failed refetch does not discard the cache. Within `maxJwksAge` its CONFIRMED keys keep
 *   answering; beyond it the answer is stale — except an applied tombstone, which is revoked at
 *   any age. A set never fetched has nothing to fall back on and stays unavailable.
 * - A failed refetch of a cached set is retried at most once per cooldown, so an outage does not
 *   turn every verification into an outbound request.
 * - Age is measured on a monotonic clock; claim checks use the wall clock.
 *
 * One process runs one verification at a time, so unlike the other SDKs there is no fetch in
 * flight to share.
 */
final class KeySetCache
{
    public const SUPPORTED_ALGORITHMS = ['RS256', 'ES256'];

    /** How long a `kid` miss — or a failed refetch of a cached set — suppresses further fetches. */
    public const KEY_MISS_COOLDOWN_MS = 30_000;

    /** How long a fetched key set is reused without refetching. */
    public const KEY_SET_TTL_MS = 10 * 60_000;

    /** Default `maxJwksAge`, in seconds. */
    public const DEFAULT_MAX_JWKS_AGE_SECONDS = 24 * 3600;

    /** Largest accepted `maxJwksAge`, in seconds — below the issuer's ~30-day lead on its next key. */
    public const MAX_JWKS_AGE_CAP_SECONDS = 25 * 86_400;

    private const JWKS_TIMEOUT_SECONDS = 5.0;

    /** A key set is kilobytes; anything larger is not one. */
    private const MAX_JWKS_BYTES = 524288;

    /** The key type each accepted algorithm needs. */
    private const KEY_TYPES = ['RS256' => 'RSA', 'ES256' => 'EC'];

    private static ?Clock $clock = null;

    /** @var (callable(array{type: string, kid: string, message: string}): void)|null */
    private static $eventListener = null;

    private KeySetState $state;

    /** @var array<string, \Firebase\JWT\Key|null> Imported keys by material + algorithm; null records a JWK that failed to import. */
    private array $imported = [];

    private ?float $lastMissAt = null;

    private ?float $lastFailedAt = null;

    private bool $forceNext = false;

    private readonly float $maxJwksAgeMs;

    public function __construct(
        private readonly HttpClient $http,
        private readonly string $jwksUri,
        int|float $maxJwksAgeSeconds,
    ) {
        $this->maxJwksAgeMs = $maxJwksAgeSeconds * 1000;
        $this->state = new KeySetState();
    }

    /** Test seam — replaces both clocks; null restores the system clocks. */
    public static function setClockForTests(?Clock $clock): void
    {
        self::$clock = $clock;
    }

    /**
     * Test seam — receives every key-set event; null detaches. The SDK has no logger to report
     * them to.
     *
     * @param (callable(array{type: string, kid: string, message: string}): void)|null $listener
     */
    public static function setEventListenerForTests(?callable $listener): void
    {
        self::$eventListener = $listener;
    }

    public static function clock(): Clock
    {
        return self::$clock ??= new class () implements Clock {
            public function monotonicMs(): float
            {
                return hrtime(true) / 1e6;
            }

            public function wallMs(): float
            {
                return microtime(true) * 1000;
            }
        };
    }

    /** `$value` as seconds, or `InvalidArgumentException` unless it is a number in (0, 25 days]. */
    public static function validateMaxJwksAge(mixed $value): int|float
    {
        if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value)
            || $value <= 0 || $value > self::MAX_JWKS_AGE_CAP_SECONDS) {
            throw new \InvalidArgumentException(sprintf(
                'maxJwksAge must be a number of seconds greater than 0 and at most %d (25 days)',
                self::MAX_JWKS_AGE_CAP_SECONDS
            ));
        }
        return $value;
    }

    /**
     * The keys that may verify a token of `$family` carrying `$kid` (null: none) — or why none may.
     * Fetches when the set is older than the TTL, after `forceRefresh()`, or once per cooldown when
     * `$kid` is unknown.
     *
     * @return array{kind: 'keys', candidates: list<array{key: \Firebase\JWT\Key, record: KeyRecord}>, registryLive: bool}
     *     |array{kind: 'revoked'|'wrong_purpose', kid: string}
     *     |array{kind: 'unknown'}
     *     |array{kind: 'stale', cacheAgeSeconds: int|null}
     *     |array{kind: 'unavailable', message: string}
     */
    public function resolve(?string $kid, string $alg, string $family): array
    {
        $now = self::clock()->monotonicMs();
        $forced = $this->forceNext;
        if (!$forced && $this->isFresh($now)) {
            $cached = $this->resolveFromState($this->state, $kid, $family, $alg);
            if ($cached['kind'] !== 'unknown') {
                return $cached;
            }
            if ($this->lastMissAt !== null && $now - $this->lastMissAt < self::KEY_MISS_COOLDOWN_MS) {
                // The kid may have been published since the last refetch: "could not check".
                return ['kind' => 'unavailable', 'message' => 'unknown key; the key set is refetched again after the cooldown'];
            }
        }

        $recentlyFailed = !$forced
            && $this->state->fetchedAt !== null
            && $this->lastFailedAt !== null
            && $now - $this->lastFailedAt < self::KEY_MISS_COOLDOWN_MS;
        $failure = $recentlyFailed
            ? 'JWKS refetch failed recently; it is retried after the cooldown'
            : $this->runFetch();

        if ($failure !== null) {
            if ($this->state->fetchedAt === null) {
                return ['kind' => 'unavailable', 'message' => $failure];
            }
            // An applied tombstone is final whatever the cache's age: no refetch could lift it.
            $known = KeySet::selectKeys($this->state, $kid, $family);
            if ($known['kind'] === 'revoked') {
                return $known;
            }
            $age = $this->cacheAgeMs(self::clock()->monotonicMs());
            if ($age > $this->maxJwksAgeMs) {
                return ['kind' => 'stale', 'cacheAgeSeconds' => is_finite($age) ? (int) floor($age / 1000) : null];
            }
            $fallback = KeySet::withConfirmedKeysOnly($this->state);
            // A no-kid token tries every candidate: if the fallback lost one, a signature matching
            // none of the rest may still be that key's — "could not check", not a forgery.
            if ($kid === null && self::candidateCount($fallback, $family) < self::candidateCount($this->state, $family)) {
                return ['kind' => 'unavailable', 'message' => $failure];
            }
            $cached = $this->resolveFromState($fallback, $kid, $family, $alg);
            return $cached['kind'] === 'unknown' ? ['kind' => 'unavailable', 'message' => $failure] : $cached;
        }

        $resolved = $this->resolveFromState($this->state, $kid, $family, $alg);
        if ($resolved['kind'] === 'unknown') {
            $this->lastMissAt = self::clock()->monotonicMs();
        }
        return $resolved;
    }

    /** Make the next resolution refetch, ignoring the TTL and both cooldowns. Keeps what is known. */
    public function forceRefresh(): void
    {
        $this->forceNext = true;
    }

    /** Forget every applied and pending tombstone — the documented manual reset. */
    public function clearTombstones(): void
    {
        $this->state = KeySet::withoutTombstones($this->state);
    }

    /**
     * What the cache holds, for tests.
     *
     * @return array{cacheAgeSeconds: int|null, registryLive: bool, vanishedKids: list<string>}
     */
    public function diagnostics(): array
    {
        $age = $this->cacheAgeMs(self::clock()->monotonicMs());
        $vanished = [];
        foreach ($this->state->keys as $record) {
            if ($record->vanished && $record->kid !== null) {
                $vanished[] = $record->kid;
            }
        }
        return [
            'cacheAgeSeconds' => is_finite($age) ? (int) floor($age / 1000) : null,
            'registryLive' => KeySet::isRegistryLive($this->state),
            'vanishedKids' => $vanished,
        ];
    }

    private function cacheAgeMs(float $now): float
    {
        if ($this->state->fetchedAt === null) {
            return INF;
        }
        return $now - $this->state->fetchedAt->monotonicMs;
    }

    private function isFresh(float $now): bool
    {
        return $this->cacheAgeMs($now) < self::KEY_SET_TTL_MS;
    }

    /** One fetch, merged into the state. Returns null on success, else why it failed. */
    private function runFetch(): ?string
    {
        $this->forceNext = false;
        $failure = null;
        try {
            $body = $this->fetchDocument();
            $merged = KeySet::mergeFetchedDocument($this->state, $body, new ClockReading(
                self::clock()->monotonicMs(),
                self::clock()->wallMs(),
            ));
            foreach ($merged['events'] as $event) {
                self::emit($event);
            }
            if ($merged['accepted']) {
                $this->state = $merged['state'];
                $this->lastFailedAt = null;
                return null;
            }
            $failure = $merged['events'][0]['message'] ?? 'JWKS refused';
        } catch (\Exception $e) {
            $failure = $e->getMessage() !== '' ? $e->getMessage() : 'JWKS fetch failed';
        }
        // Only a set with a cache to fall back on is held back; one never fetched is retried every call.
        if ($this->state->fetchedAt !== null) {
            $this->lastFailedAt = self::clock()->monotonicMs();
        }
        return $failure;
    }

    /** @param array{type: string, kid: string, message: string} $event */
    private static function emit(array $event): void
    {
        if (self::$eventListener === null) {
            return;
        }
        try {
            (self::$eventListener)($event);
        } catch (\Throwable) {
            // A throwing listener must not turn into a verification failure.
        }
    }

    private function fetchDocument(): \stdClass
    {
        $response = $this->http->fetchPublic(
            $this->jwksUri,
            self::JWKS_TIMEOUT_SECONDS,
            ['accept' => 'application/json'],
            self::MAX_JWKS_BYTES,
        );
        if ($response['status'] < 200 || $response['status'] >= 300) {
            throw new \RuntimeException('JWKS fetch failed with HTTP ' . $response['status']);
        }
        if ($response['declared_length'] !== null && $response['declared_length'] > self::MAX_JWKS_BYTES) {
            throw new \RuntimeException('JWKS response is too large');
        }
        if (strlen($response['body']) > self::MAX_JWKS_BYTES) {
            throw new \RuntimeException('JWKS response is too large');
        }
        $body = json_decode($response['body'], false);
        if (!$body instanceof \stdClass) {
            throw new \RuntimeException('JWKS response is not a JSON object');
        }
        if (!is_array($body->keys ?? null)) {
            throw new \RuntimeException('JWKS response has no `keys` array');
        }
        return $body;
    }

    /**
     * @return array{kind: 'keys', candidates: list<array{key: \Firebase\JWT\Key, record: KeyRecord}>, registryLive: bool}
     *     |array{kind: 'revoked'|'wrong_purpose', kid: string}
     *     |array{kind: 'unknown'}
     */
    private function resolveFromState(KeySetState $state, ?string $kid, string $family, string $alg): array
    {
        $selection = KeySet::selectKeys($state, $kid, $family);
        if ($selection['kind'] !== 'candidates') {
            return $selection;
        }
        $candidates = [];
        foreach ($selection['records'] as $record) {
            $key = $this->importRecord($record, $alg);
            if ($key !== null) {
                $candidates[] = ['key' => $key, 'record' => $record];
            }
        }
        if ($candidates === []) {
            return ['kind' => 'unknown'];
        }
        return ['kind' => 'keys', 'candidates' => $candidates, 'registryLive' => KeySet::isRegistryLive($state)];
    }

    private function importRecord(KeyRecord $record, string $alg): ?\Firebase\JWT\Key
    {
        // A key declared for another algorithm, or for anything but signatures (RFC 7517 §4.2,
        // §4.3), is not a candidate. Checked before the import cache, which is keyed by material.
        $jwk = $record->jwk;
        if (is_string($jwk['alg'] ?? null) && $jwk['alg'] !== $alg) {
            return null;
        }
        if (array_key_exists('use', $jwk) && $jwk['use'] !== 'sig') {
            return null;
        }
        if (array_key_exists('key_ops', $jwk)
            && !(is_array($jwk['key_ops']) && in_array('verify', $jwk['key_ops'], true))) {
            return null;
        }
        $cacheKey = $record->fingerprint . '|' . $alg;
        if (array_key_exists($cacheKey, $this->imported)) {
            return $this->imported[$cacheKey];
        }
        $key = null;
        // The key type the algorithm needs: an RSA key is no candidate for an ES256 token, exactly
        // as an import for that algorithm refuses it elsewhere.
        if (($jwk['kty'] ?? null) === (self::KEY_TYPES[$alg] ?? null)
            && ($alg !== 'ES256' || ($jwk['crv'] ?? null) === 'P-256')) {
            $material = ['alg' => $alg];
            foreach (['kty', 'crv', 'x', 'y', 'n', 'e'] as $member) {
                if (is_string($jwk[$member] ?? null)) {
                    $material[$member] = $jwk[$member];
                }
            }
            try {
                $key = \Firebase\JWT\JWK::parseKey($material, $alg);
            } catch (\Exception) {
                $key = null;
            }
            // php-jwt does not check that an EC point lies on its curve. OpenSSL refuses to load one
            // that does not, so a key it cannot load is no candidate — as the other SDKs' imports
            // refuse it — rather than a key every signature then "fails" against.
            if ($key !== null && @openssl_pkey_get_public($key->getKeyMaterial()) === false) {
                $key = null;
            }
        }
        return $this->imported[$cacheKey] = $key;
    }

    private static function candidateCount(KeySetState $state, string $family): int
    {
        $selection = KeySet::selectKeys($state, null, $family);
        return $selection['kind'] === 'candidates' ? count($selection['records']) : 0;
    }
}

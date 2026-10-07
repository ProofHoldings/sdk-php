<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

/**
 * The key-rotation rules, free of I/O.
 *
 * A function-for-function port of `packages/delegation-verifier/src/keyset.ts`: how a fetched JWKS
 * is merged into what the verifier already knows, and which key — if any — may verify a token of a
 * given family. The shared cases in `sdks/shared/fixtures/jwks_rotation/` pin the outcome of every
 * rule across all five verifiers, so change a rule there first and port it here.
 *
 * The persistence half of the original (state serialization) is not ported: this SDK keeps its key
 * set in memory only.
 *
 * Four properties are load-bearing:
 *
 * - One response is never trusted for long. Anything that would outlive the next fetch — a key
 *   being remembered, a window narrowing, a tombstone — takes effect only when a second fetch, at
 *   least five minutes after the first sighting, says the same. A key verifies from its first
 *   sighting, but until it is confirmed the next document replaces or drops it.
 * - A fetch is MERGED, never swapped in. A CONFIRMED registry key that disappears keeps its
 *   last-known state, never gets new key material, and its window may narrow (by the rule above)
 *   but never widen — a document that widens one is refused whole.
 * - An applied tombstone is sticky until every token the key signed is dead.
 * - "Registry live" is a property of the merged set (some key carries `proof_purpose`). Until then
 *   the set is in tolerant mode and behaves exactly as it did before rotation existed.
 *
 * Documents arrive decoded with JSON objects as `\stdClass`, so an object and an array stay
 * distinguishable exactly as they are in the original.
 */
final class KeySet
{
    public const KEY_FAMILIES = ['proof', 'delegation', 'status-list'];

    /** Clock skew tolerated on each edge of a key's `[not_before, not_after]` window. */
    public const KEY_WINDOW_SKEW_SECONDS = 300;

    /** Minimum spacing between the two fetches that confirm a key, a narrowed window or a tombstone. */
    public const CONFIRM_SPACING_MS = 5 * 60_000;

    /** An applied tombstone is kept until `revoked_at` plus this. */
    public const TOMBSTONE_RETENTION_SECONDS = 365 * 86_400;

    private const MATERIAL_MEMBERS = ['kty', 'crv', 'x', 'y', 'n', 'e'];

    /** A whole number as `Number.isInteger` reads it, or null. */
    public static function wholeNumber(mixed $value): int|float|null
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value) && is_finite($value) && floor($value) === $value) {
            return $value;
        }
        return null;
    }

    private static function isKeyFamily(mixed $value): bool
    {
        return is_string($value) && in_array($value, self::KEY_FAMILIES, true);
    }

    /** @param array<string, mixed> $jwk */
    private static function jsonOf(array $jwk, string ...$members): string
    {
        $values = [];
        foreach ($members as $member) {
            $values[] = $jwk[$member] ?? null;
        }
        return (string) json_encode($values, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /** @param array<string, mixed> $jwk */
    public static function parseKeyRecord(array $jwk, ClockReading $firstSeen): KeyRecord
    {
        $record = new KeyRecord();
        $record->jwk = $jwk;
        $record->fingerprint = self::jsonOf($jwk, ...self::MATERIAL_MEMBERS);
        $record->usage = self::jsonOf($jwk, 'alg', 'use', 'key_ops');
        $record->hasPurposeMember = array_key_exists('proof_purpose', $jwk);
        $record->legacy = ($jwk['proof_legacy'] ?? null) === true;
        $record->firstSeen = $firstSeen;
        if (is_string($jwk['kid'] ?? null) && $jwk['kid'] !== '') {
            $record->kid = $jwk['kid'];
        }
        if (self::isKeyFamily($jwk['proof_purpose'] ?? null)) {
            $record->purpose = $jwk['proof_purpose'];
        }
        $record->notBefore = self::wholeNumber($jwk['not_before'] ?? null);
        $record->notAfter = self::wholeNumber($jwk['not_after'] ?? null);
        $record->maxTokenLifetime = self::wholeNumber($jwk['max_token_lifetime'] ?? null);
        return $record;
    }

    private static function hasWindow(KeyRecord $record): bool
    {
        return $record->notBefore !== null && $record->notAfter !== null && $record->maxTokenLifetime !== null;
    }

    /**
     * A key the issuer's registry describes — it carries `proof_purpose` or `proof_legacy` — AND
     * that two fetches at least `CONFIRM_SPACING_MS` apart served unchanged. The ONE predicate
     * behind every rule that outlives a fetch: material lock, no widening, pending narrowing,
     * retained when it vanishes.
     */
    private static function isProtectedRecord(KeyRecord $record): bool
    {
        return $record->confirmed && ($record->hasPurposeMember || $record->legacy);
    }

    /** Equal as numbers, and absent only together — `null == 0` must not count as equal. */
    private static function sameNumber(int|float|null $a, int|float|null $b): bool
    {
        if ($a === null || $b === null) {
            return $a === null && $b === null;
        }
        return $a == $b;
    }

    /**
     * Whether two records describe the same key: material, usage, purpose, legacy marker and
     * window. Usage counts because the record kept is the one first seen: a poisoned first
     * sighting marked `use: "enc"` must be replaced by the honest description, not confirmed by it.
     */
    private static function describesSameKey(KeyRecord $a, KeyRecord $b): bool
    {
        return $a->fingerprint === $b->fingerprint
            && $a->usage === $b->usage
            && $a->hasPurposeMember === $b->hasPurposeMember
            && $a->purpose === $b->purpose
            && $a->legacy === $b->legacy
            && self::sameNumber($a->notBefore, $b->notBefore)
            && self::sameNumber($a->notAfter, $b->notAfter)
            && self::sameNumber($a->maxTokenLifetime, $b->maxTokenLifetime);
    }

    private static function spacingElapsed(ClockReading $since, ClockReading $now): bool
    {
        return $now->monotonicMs - $since->monotonicMs >= self::CONFIRM_SPACING_MS;
    }

    public static function isRegistryLive(KeySetState $state): bool
    {
        foreach ($state->keys as $record) {
            if ($record->hasPurposeMember) {
                return true;
            }
        }
        foreach ($state->anonymous as $record) {
            if ($record->hasPurposeMember) {
                return true;
            }
        }
        return false;
    }

    private static function wallSeconds(ClockReading $reading): float
    {
        return floor($reading->wallMs / 1000);
    }

    /** Whether a kid may still be signing: legacy, window unknown, or not yet past `not_after`. */
    private static function isLiveKey(KeyRecord $record, float $nowSeconds): bool
    {
        if ($record->legacy || !self::hasWindow($record)) {
            return true;
        }
        return $nowSeconds <= $record->notAfter + self::KEY_WINDOW_SKEW_SECONDS;
    }

    /** @return list<array{kid: string, revokedAt: int|float}> */
    private static function parseTombstones(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $tombstones = [];
        foreach ($value as $entry) {
            if (!$entry instanceof \stdClass) {
                continue;
            }
            $kid = $entry->kid ?? null;
            if (!is_string($kid) || $kid === '') {
                continue;
            }
            $revokedAt = self::wholeNumber($entry->revoked_at ?? null);
            if ($revokedAt === null) {
                continue;
            }
            $tombstones[] = ['kid' => $kid, 'revokedAt' => $revokedAt];
        }
        return $tombstones;
    }

    /**
     * Merge one fetched JWKS body (already known to be an object with a `keys` array) into
     * `$state`. Pure: returns a new state and the events the caller should report.
     *
     * @return array{state: KeySetState, events: list<array{type: string, kid: string, message: string}>, accepted: bool}
     */
    public static function mergeFetchedDocument(KeySetState $state, \stdClass $body, ClockReading $now): array
    {
        $nowSeconds = self::wallSeconds($now);
        $served = [];
        $anonymous = [];
        foreach (is_array($body->keys ?? null) ? $body->keys : [] as $entry) {
            if (!$entry instanceof \stdClass) {
                continue;
            }
            $record = self::parseKeyRecord(get_object_vars($entry), $now);
            if ($record->kid !== null) {
                $served[$record->kid] = $record;
            } else {
                $anonymous[] = $record;
            }
        }

        // Widening is checked before anything is touched: such a document is refused whole.
        foreach ($served as $incoming) {
            $kid = $incoming->kid;
            $known = $state->keys[$kid] ?? null;
            if ($known === null || !self::isProtectedRecord($known) || !self::hasWindow($known) || !self::hasWindow($incoming)) {
                continue;
            }
            if ($incoming->notBefore < $known->notBefore || $incoming->notAfter > $known->notAfter) {
                return [
                    'state' => $state,
                    'accepted' => false,
                    'events' => [[
                        'type' => 'window_widening_rejected',
                        'kid' => $kid,
                        'message' => sprintf('JWKS refused: it widens the window of confirmed key "%s"', $kid),
                    ]],
                ];
            }
        }

        $next = clone $state;
        $events = [];
        $tombstones = self::parseTombstones($body->proof_tombstones ?? null);
        $tombstonedNow = [];
        foreach ($tombstones as $tombstone) {
            $tombstonedNow[$tombstone['kid']] = true;
        }

        foreach ($served as $incoming) {
            $kid = $incoming->kid;
            $known = $next->keys[$kid] ?? null;
            // Not protected yet: the latest document describes the key, exactly as before rotation
            // existed. Serving the same description again, far enough apart, confirms it.
            if ($known === null || !self::isProtectedRecord($known)) {
                if ($known !== null && self::describesSameKey($known, $incoming)) {
                    if (!$known->confirmed && self::spacingElapsed($known->firstSeen, $now)) {
                        $known->confirmed = true;
                    }
                } else {
                    $next->keys[$kid] = $incoming;
                }
                continue;
            }
            $known->vanished = false;
            if ($known->fingerprint !== $incoming->fingerprint) {
                $known->pendingWindow = null;
                $events[] = [
                    'type' => 'key_material_changed',
                    'kid' => $kid,
                    'message' => sprintf('JWKS serves different key material for confirmed key "%s"; the first-seen key is kept', $kid),
                ];
                continue;
            }
            if (!self::hasWindow($known) || !self::hasWindow($incoming)) {
                continue;
            }
            if ($incoming->notBefore == $known->notBefore && $incoming->notAfter == $known->notAfter) {
                $known->pendingWindow = null;
                continue;
            }
            $pending = $known->pendingWindow;
            if ($pending === null || $pending['notBefore'] != $incoming->notBefore || $pending['notAfter'] != $incoming->notAfter) {
                $known->pendingWindow = [
                    'notBefore' => $incoming->notBefore,
                    'notAfter' => $incoming->notAfter,
                    'firstSeen' => $now,
                ];
                $events[] = [
                    'type' => 'window_narrowing_pending',
                    'kid' => $kid,
                    'message' => sprintf(
                        'JWKS narrows the window of confirmed key "%s"; applied only if a fetch at least 5 minutes later serves the same window',
                        $kid
                    ),
                ];
                continue;
            }
            if (!self::spacingElapsed($pending['firstSeen'], $now)) {
                continue;
            }
            $known->notBefore = $pending['notBefore'];
            $known->notAfter = $pending['notAfter'];
            $known->jwk = array_merge($known->jwk, [
                'not_before' => $pending['notBefore'],
                'not_after' => $pending['notAfter'],
            ]);
            $known->pendingWindow = null;
            $events[] = [
                'type' => 'window_narrowing_applied',
                'kid' => $kid,
                'message' => sprintf('narrowed window of key "%s" confirmed by a second fetch and applied', $kid),
            ];
        }

        foreach ($next->keys as $index => $known) {
            $kid = (string) $known->kid;
            if (isset($served[$kid])) {
                continue;
            }
            if (!self::isProtectedRecord($known)) {
                unset($next->keys[$index]);
                continue;
            }
            if (self::hasWindow($known)) {
                if ($nowSeconds > $known->notAfter + $known->maxTokenLifetime + self::KEY_WINDOW_SKEW_SECONDS) {
                    unset($next->keys[$index]);
                    continue;
                }
            } elseif (!$known->legacy) {
                unset($next->keys[$index]);
                continue;
            }
            // A narrowing is confirmed by consecutive documents serving it; one that omits the kid
            // breaks the run.
            $known->pendingWindow = null;
            if (!$known->vanished && !isset($tombstonedNow[$kid])) {
                $known->vanished = true;
                $events[] = [
                    'type' => 'kid_vanished',
                    'kid' => $kid,
                    'message' => sprintf('key "%s" is no longer served before its retention ended; its last-known state is kept', $kid),
                ];
            }
        }

        $next->anonymous = $anonymous;

        foreach ($next->pending as $index => $pending) {
            if (!isset($tombstonedNow[$pending['kid']])) {
                unset($next->pending[$index]);
            }
        }
        foreach ($tombstones as $tombstone) {
            $kid = $tombstone['kid'];
            if (isset($next->applied[$kid])) {
                continue;
            }
            $pending = $next->pending[$kid] ?? null;
            if ($pending !== null) {
                if (self::spacingElapsed($pending['firstSeen'], $now)) {
                    unset($next->pending[$kid]);
                    $next->applied[$kid] = ['kid' => $kid, 'revokedAt' => $pending['revokedAt']];
                    $events[] = [
                        'type' => 'tombstone_applied',
                        'kid' => $kid,
                        'message' => sprintf('tombstone for key "%s" confirmed by a second fetch and applied', $kid),
                    ];
                }
                continue;
            }
            $next->pending[$kid] = ['kid' => $kid, 'revokedAt' => $tombstone['revokedAt'], 'firstSeen' => $now];
            $events[] = [
                'type' => 'tombstone_pending',
                'kid' => $kid,
                'message' => sprintf(
                    'tombstone for key "%s" seen; applied only if a fetch at least 5 minutes later still carries it',
                    $kid
                ),
            ];
            // "Last seen live" is read from what the verifier knew BEFORE this document, which may
            // itself have dropped the key.
            $known = $state->keys[$kid] ?? null;
            if ($known !== null && self::isLiveKey($known, $nowSeconds)) {
                $events[] = [
                    'type' => 'tombstone_for_live_key',
                    'kid' => $kid,
                    'message' => sprintf('ANOMALY: tombstone names key "%s", which was last seen live', $kid),
                ];
            }
        }

        foreach ($next->applied as $index => $applied) {
            if ($nowSeconds > $applied['revokedAt'] + self::TOMBSTONE_RETENTION_SECONDS) {
                unset($next->applied[$index]);
            }
        }

        $next->fetchedAt = $now;
        return ['state' => $next, 'events' => $events, 'accepted' => true];
    }

    /**
     * Which keys may verify a token of `$family` carrying `$kid` (null: the token has none).
     * Checks the applied tombstone and the key's purpose; the signature and the window are the
     * caller's next steps, in that order.
     *
     * @return array{kind: 'candidates', records: list<KeyRecord>}|array{kind: 'revoked'|'wrong_purpose', kid: string}|array{kind: 'unknown'}
     */
    public static function selectKeys(KeySetState $state, ?string $kid, string $family): array
    {
        $registryLive = self::isRegistryLive($state);
        if ($kid !== null) {
            if (isset($state->applied[$kid])) {
                return ['kind' => 'revoked', 'kid' => $kid];
            }
            $record = $state->keys[$kid] ?? null;
            if ($record === null) {
                return ['kind' => 'unknown'];
            }
            if ($record->legacy || !$registryLive) {
                return ['kind' => 'candidates', 'records' => [$record]];
            }
            if ($record->purpose !== $family) {
                return ['kind' => 'wrong_purpose', 'kid' => $kid];
            }
            return ['kind' => 'candidates', 'records' => [$record]];
        }

        // No `kid`: once the registry is live only a key marked legacy may answer — trying every
        // key would let a token borrow any family's key and skip the purpose check.
        $records = [];
        foreach ([...$state->anonymous, ...array_values($state->keys)] as $record) {
            if ($record->kid !== null && isset($state->applied[$record->kid])) {
                continue;
            }
            if ($registryLive && !$record->legacy) {
                continue;
            }
            $records[] = $record;
        }
        return $records === [] ? ['kind' => 'unknown'] : ['kind' => 'candidates', 'records' => $records];
    }

    /**
     * The window and lifetime rules for a key whose signature has verified: null when the token
     * passes, else `outside_key_window` or `token_lifetime_exceeded`.
     */
    public static function checkKeyPolicy(KeyRecord $record, mixed $iat, mixed $exp, bool $registryLive): ?string
    {
        if (!$registryLive || $record->legacy) {
            return null;
        }
        if (!self::hasWindow($record)) {
            return 'outside_key_window';
        }
        $issuedAt = self::wholeNumber($iat);
        if ($issuedAt === null) {
            return 'outside_key_window';
        }
        if ($issuedAt < $record->notBefore - self::KEY_WINDOW_SKEW_SECONDS) {
            return 'outside_key_window';
        }
        if ($issuedAt > $record->notAfter + self::KEY_WINDOW_SKEW_SECONDS) {
            return 'outside_key_window';
        }
        if ((!is_int($exp) && !is_float($exp)) || $exp > $issuedAt + $record->maxTokenLifetime) {
            return 'token_lifetime_exceeded';
        }
        return null;
    }

    /**
     * The part of a cached set that may answer after a refetch FAILED: confirmed keys only. An
     * unconfirmed key is trusted until the next document, and a failing fetch must not stretch
     * that to `maxJwksAge` — otherwise one poisoned response followed by an outage would keep its
     * key, and the registry mode it switched on, for a day.
     */
    public static function withConfirmedKeysOnly(KeySetState $state): KeySetState
    {
        $next = clone $state;
        foreach ($next->keys as $index => $record) {
            if (!$record->confirmed) {
                unset($next->keys[$index]);
            }
        }
        $next->anonymous = [];
        return $next;
    }

    /** Drop every applied and pending tombstone — the documented manual reset. */
    public static function withoutTombstones(KeySetState $state): KeySetState
    {
        $next = clone $state;
        $next->applied = [];
        $next->pending = [];
        return $next;
    }
}

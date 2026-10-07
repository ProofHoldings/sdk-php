<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

/**
 * One key of the merged key set, as `KeySet` reads it from a JWK.
 *
 * Mutable on purpose, like the original's records: `KeySet::mergeFetchedDocument` works on a clone
 * of the state (see `KeySetState::__clone`) and updates its records in place.
 */
final class KeyRecord
{
    /** Null for a key served without a `kid`. */
    public ?string $kid = null;

    /** @var array<string, mixed> The JWK as last accepted, with any narrowing of its window applied. */
    public array $jwk;

    /** Identity of the key material: a protected kid keeps its first material, an unprotected one takes the latest. */
    public string $fingerprint;

    /** `alg`, `use` and `key_ops` — the members that decide whether the key may verify at all. */
    public string $usage;

    public bool $hasPurposeMember;

    public ?string $purpose = null;

    public bool $legacy;

    public int|float|null $notBefore = null;

    public int|float|null $notAfter = null;

    public int|float|null $maxTokenLifetime = null;

    public bool $vanished = false;

    /** When this exact description of the key was first served. */
    public ClockReading $firstSeen;

    /** Served unchanged by two fetches at least `KeySet::CONFIRM_SPACING_MS` apart. */
    public bool $confirmed = false;

    /** @var array{notBefore: int|float, notAfter: int|float, firstSeen: ClockReading}|null A narrower window served for a confirmed key. */
    public ?array $pendingWindow = null;
}

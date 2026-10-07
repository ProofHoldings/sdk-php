<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

/**
 * The merged key set. `keys` keeps its insertion order, as the original's Map does, so a no-kid
 * token tries its candidates in the same order everywhere.
 *
 * Kids are array keys: read a record's kid from `KeyRecord::$kid`, never from the key, which PHP
 * turns into an int for a numeric kid.
 */
final class KeySetState
{
    /** @var array<string, KeyRecord> */
    public array $keys = [];

    /** @var list<KeyRecord> Keys served without a `kid` — tolerant mode only ever needs them. */
    public array $anonymous = [];

    /** @var array<string, array{kid: string, revokedAt: int|float, firstSeen: ClockReading}> */
    public array $pending = [];

    /** @var array<string, array{kid: string, revokedAt: int|float}> */
    public array $applied = [];

    public ?ClockReading $fetchedAt = null;

    public function __clone()
    {
        foreach ($this->keys as $kid => $record) {
            $this->keys[$kid] = clone $record;
        }
        foreach ($this->anonymous as $index => $record) {
            $this->anonymous[$index] = clone $record;
        }
    }
}

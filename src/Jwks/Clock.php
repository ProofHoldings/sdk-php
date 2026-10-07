<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

/** Both clocks the key set reads: monotonic for its own intervals, wall for claim checks. */
interface Clock
{
    public function monotonicMs(): float;

    public function wallMs(): float;
}

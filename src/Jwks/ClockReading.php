<?php

declare(strict_types=1);

namespace ProofHoldings\Jwks;

/** Both clocks at one instant: monotonic for in-process intervals, wall for claims. */
final class ClockReading
{
    public function __construct(
        public readonly float $monotonicMs,
        public readonly float $wallMs,
    ) {
    }
}

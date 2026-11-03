<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

/**
 * `402 balance_empty` — the account has used the proofs its plan includes this month and its prepaid
 * balance is at or below zero, so new proof requests are refused until it tops up. Any method that
 * creates a proof request can throw it once billing is live; proofs already issued keep working.
 */
class BalanceEmptyException extends ProofException
{
    public function __construct(
        string $message,
        string $code,
        mixed $details = null,
        ?string $requestId = null,
        /** Where the owner tops up the balance. */
        public readonly ?string $topUpUrl = null,
        /** This period's production proofs. */
        public readonly ?int $used = null,
        /** Proofs the plan includes per period. */
        public readonly ?int $included = null,
        /** The prepaid balance in euro cents, at or below zero. */
        public readonly ?int $balance = null,
    ) {
        parent::__construct($message, $code, 402, $details, $requestId);
    }
}

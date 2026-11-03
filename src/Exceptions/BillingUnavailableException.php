<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

/**
 * `503 billing_unavailable` — billing is not live, so checkout, the billing portal and top-ups are
 * refused. A ServerException, so an existing `catch (ServerException)` still catches it; unlike other
 * 5xx answers it is never retried, because asking again cannot change it.
 */
class BillingUnavailableException extends ServerException
{
    /** The code of the billing kill switch's refusal. */
    public const string CODE = 'billing_unavailable';

    public function __construct(string $message, int $statusCode = 503, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, self::CODE, $statusCode, $details, $requestId);
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class ValidationException extends ProofHoldingsException
{
    public function __construct(string $message, string $code, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, 400, $details, $requestId);
    }
}

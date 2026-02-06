<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class AuthenticationException extends ProofHoldingsException
{
    public function __construct(string $message, string $code, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, 401, $details, $requestId);
    }
}

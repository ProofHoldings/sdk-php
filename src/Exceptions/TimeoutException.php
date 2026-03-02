<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class TimeoutException extends ProofException
{
    public function __construct(string $message = 'Request timed out')
    {
        parent::__construct($message, 'timeout', 0);
    }
}

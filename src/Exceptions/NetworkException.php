<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class NetworkException extends ProofHoldingsException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'network_error', 0);
    }
}

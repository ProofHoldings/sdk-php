<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class PollingTimeoutException extends ProofHoldingsException
{
    public function __construct(string $message = 'Polling timed out waiting for completion')
    {
        parent::__construct($message, 'polling_timeout', 0);
    }
}

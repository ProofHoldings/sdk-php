<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class PollingCancelledException extends ProofException
{
    public function __construct(string $message = 'Polling cancelled')
    {
        parent::__construct($message, 'polling_cancelled', 0);
    }
}

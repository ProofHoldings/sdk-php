<?php

declare(strict_types=1);

namespace Proof\Exceptions;

class NetworkException extends ProofException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'network_error', 0);
    }
}

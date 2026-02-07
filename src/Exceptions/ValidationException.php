<?php

declare(strict_types=1);

namespace Proof\Exceptions;

class ValidationException extends ProofException
{
    public function __construct(string $message, string $code, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, 400, $details, $requestId);
    }
}

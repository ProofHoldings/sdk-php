<?php

declare(strict_types=1);

namespace Proof\Exceptions;

class RateLimitException extends ProofException
{
    public function __construct(string $message, string $code, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, 429, $details, $requestId);
    }
}

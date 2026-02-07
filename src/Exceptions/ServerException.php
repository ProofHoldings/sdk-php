<?php

declare(strict_types=1);

namespace Proof\Exceptions;

class ServerException extends ProofException
{
    public function __construct(string $message, string $code, int $statusCode = 500, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, $statusCode, $details, $requestId);
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Exceptions;

class ServerException extends ProofHoldingsException
{
    public function __construct(string $message, string $code, int $statusCode = 500, mixed $details = null, ?string $requestId = null)
    {
        parent::__construct($message, $code, $statusCode, $details, $requestId);
    }
}

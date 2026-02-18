<?php

declare(strict_types=1);

namespace Proof\Exceptions;

class RateLimitException extends ProofException
{
    public function __construct(
        string $message,
        string $code,
        mixed $details = null,
        ?string $requestId = null,
        /** Seconds to wait before retrying (from error response retryAfter field). */
        public readonly ?int $retryAfter = null,
        /** Number of remaining attempts before lockout (auth endpoints only). */
        public readonly ?int $remainingAttempts = null,
    ) {
        parent::__construct($message, $code, 429, $details, $requestId);
    }
}

<?php

declare(strict_types=1);

namespace Proof\Exceptions;

use RuntimeException;

class ProofException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $statusCode,
        public readonly mixed $details = null,
        public readonly ?string $requestId = null,
    ) {
        parent::__construct($message, $statusCode);
    }

    public static function fromResponse(int $statusCode, ?array $error = null): self
    {
        $code = $error['code'] ?? "http_{$statusCode}";
        $message = $error['message'] ?? "Request failed with status {$statusCode}";
        $details = $error['details'] ?? null;
        $requestId = $error['request_id'] ?? null;

        return match ($statusCode) {
            400 => new ValidationException($message, $code, $details, $requestId),
            401 => new AuthenticationException($message, $code, $details, $requestId),
            403 => new ForbiddenException($message, $code, $details, $requestId),
            404 => new NotFoundException($message, $code, $details, $requestId),
            409 => new ConflictException($message, $code, $details, $requestId),
            429 => new RateLimitException(
                $message, $code, $details, $requestId,
                isset($error['retryAfter']) ? (int) $error['retryAfter'] : null,
                isset($error['remaining_attempts']) ? (int) $error['remaining_attempts'] : null,
            ),
            default => $statusCode >= 500
                ? new ServerException($message, $code, $statusCode, $details, $requestId)
                : new static($message, $code, $statusCode, $details, $requestId),
        };
    }
}

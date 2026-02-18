<?php

declare(strict_types=1);

namespace Proof\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proof\Exceptions\ProofException;
use Proof\Exceptions\ValidationException;
use Proof\Exceptions\AuthenticationException;
use Proof\Exceptions\ForbiddenException;
use Proof\Exceptions\NotFoundException;
use Proof\Exceptions\ConflictException;
use Proof\Exceptions\RateLimitException;
use Proof\Exceptions\ServerException;

class ExceptionsTest extends TestCase
{
    #[DataProvider('statusCodeProvider')]
    public function testFromResponseMapsStatusCodes(int $status, string $expectedClass): void
    {
        $error = ProofException::fromResponse($status);
        $this->assertInstanceOf($expectedClass, $error);
    }

    public static function statusCodeProvider(): array
    {
        return [
            [400, ValidationException::class],
            [401, AuthenticationException::class],
            [403, ForbiddenException::class],
            [404, NotFoundException::class],
            [409, ConflictException::class],
            [429, RateLimitException::class],
            [500, ServerException::class],
            [502, ServerException::class],
            [503, ServerException::class],
        ];
    }

    public function testFromResponseWithBody(): void
    {
        $error = ProofException::fromResponse(400, [
            'code' => 'invalid_param',
            'message' => 'Bad input',
            'details' => ['field' => 'email'],
            'request_id' => 'req_abc',
        ]);

        $this->assertInstanceOf(ValidationException::class, $error);
        $this->assertSame('invalid_param', $error->errorCode);
        $this->assertSame('Bad input', $error->getMessage());
        $this->assertSame(['field' => 'email'], $error->details);
        $this->assertSame('req_abc', $error->requestId);
    }

    public function testFromResponseDefaults(): void
    {
        $error = ProofException::fromResponse(418);
        $this->assertInstanceOf(ProofException::class, $error);
        $this->assertSame('http_418', $error->errorCode);
        $this->assertSame(418, $error->statusCode);
    }

    public function testExceptionHierarchy(): void
    {
        $error = ProofException::fromResponse(400);
        $this->assertInstanceOf(\RuntimeException::class, $error);
        $this->assertInstanceOf(ProofException::class, $error);
        $this->assertInstanceOf(ValidationException::class, $error);
    }

    public function testRateLimitWithLockoutFields(): void
    {
        $error = ProofException::fromResponse(429, [
            'code' => 'auth_lockout',
            'message' => 'Too many attempts',
            'retryAfter' => 3600,
            'remaining_attempts' => 0,
        ]);

        $this->assertInstanceOf(RateLimitException::class, $error);
        $this->assertSame('auth_lockout', $error->errorCode);
        $this->assertSame(3600, $error->retryAfter);
        $this->assertSame(0, $error->remainingAttempts);
    }

    public function testServerExceptionForAllServerCodes(): void
    {
        foreach ([500, 502, 503] as $status) {
            $error = ProofException::fromResponse($status);
            $this->assertInstanceOf(ServerException::class, $error, "Status {$status} should be ServerException");
            $this->assertSame($status, $error->statusCode);
        }
    }
}

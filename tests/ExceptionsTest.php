<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Exceptions\ProofHoldingsException;
use ProofHoldings\Exceptions\ValidationException;
use ProofHoldings\Exceptions\AuthenticationException;
use ProofHoldings\Exceptions\ForbiddenException;
use ProofHoldings\Exceptions\NotFoundException;
use ProofHoldings\Exceptions\ConflictException;
use ProofHoldings\Exceptions\RateLimitException;
use ProofHoldings\Exceptions\ServerException;

class ExceptionsTest extends TestCase
{
    #[DataProvider('statusCodeProvider')]
    public function testFromResponseMapsStatusCodes(int $status, string $expectedClass): void
    {
        $error = ProofHoldingsException::fromResponse($status);
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
        $error = ProofHoldingsException::fromResponse(400, [
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
        $error = ProofHoldingsException::fromResponse(418);
        $this->assertInstanceOf(ProofHoldingsException::class, $error);
        $this->assertSame('http_418', $error->errorCode);
        $this->assertSame(418, $error->statusCode);
    }

    public function testExceptionHierarchy(): void
    {
        $error = ProofHoldingsException::fromResponse(400);
        $this->assertInstanceOf(\RuntimeException::class, $error);
        $this->assertInstanceOf(ProofHoldingsException::class, $error);
        $this->assertInstanceOf(ValidationException::class, $error);
    }

    public function testServerExceptionForAllServerCodes(): void
    {
        foreach ([500, 502, 503] as $status) {
            $error = ProofHoldingsException::fromResponse($status);
            $this->assertInstanceOf(ServerException::class, $error, "Status {$status} should be ServerException");
            $this->assertSame($status, $error->statusCode);
        }
    }
}

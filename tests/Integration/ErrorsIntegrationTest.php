<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use ProofHoldings\Exceptions\AuthenticationException;
use ProofHoldings\Exceptions\ConflictException;
use ProofHoldings\Exceptions\ForbiddenException;
use ProofHoldings\Exceptions\NotFoundException;
use ProofHoldings\Exceptions\ProofException;
use ProofHoldings\Exceptions\RateLimitException;
use ProofHoldings\Exceptions\ServerException;
use ProofHoldings\Exceptions\ValidationException;

class ErrorsIntegrationTest extends IntegrationTestCase
{
    /** @return array<string,array{0:int,1:class-string<\Throwable>}> */
    public static function errorMap(): array
    {
        return [
            '400' => [400, ValidationException::class],
            '401' => [401, AuthenticationException::class],
            '403' => [403, ForbiddenException::class],
            '404' => [404, NotFoundException::class],
            '409' => [409, ConflictException::class],
            '429' => [429, RateLimitException::class],
            '500' => [500, ServerException::class],
        ];
    }

    /** @param class-string<\Throwable> $exc */
    #[DataProvider('errorMap')]
    public function testStatusMapsToException(int $status, string $exc): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $proof = $this->makeClient([
            $this->jsonResponse($status, ['error' => ['code' => 'x', 'message' => 'boom']]),
        ]);

        $this->expectException($exc);
        $proof->verifications->retrieve('v_x');
    }

    public function testRateLimitExceptionIsProofException(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $proof = $this->makeClient([
            $this->jsonResponse(429, [
                'error' => ['code' => 'rate_limited', 'message' => 'slow'],
            ]),
        ]);

        try {
            $proof->verifications->retrieve('v_x');
            $this->fail('expected throw');
        } catch (RateLimitException $e) {
            $this->assertInstanceOf(ProofException::class, $e);
        }
    }
}

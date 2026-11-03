<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Proof;
use ProofHoldings\Types\Confirmation;

/**
 * The shared decision-signature cases (`sdks/shared/fixtures/decision_signature/cases.json`)
 * through `$proof->confirmations->verifyDecisionSignature` — a local check, given the API's array
 * and the `Confirmation` that `retrieve()` returns.
 */
final class DecisionSignatureFixturesTest extends TestCase
{
    /** @return array<string, array{array<string, mixed>}> */
    public static function cases(): array
    {
        $fixture = json_decode(
            (string) file_get_contents(__DIR__ . '/../../shared/fixtures/decision_signature/cases.json'),
            true,
        );
        $cases = [];
        foreach ($fixture['cases'] as $case) {
            $cases[$case['name']] = [$case];
        }
        return $cases;
    }

    public function testReadsEveryCase(): void
    {
        self::assertNotEmpty(self::cases());
    }

    /** @param array<string, mixed> $case */
    #[DataProvider('cases')]
    public function testCaseFromTheApiArray(array $case): void
    {
        $proof = new Proof('pk_test_decision_signature', 'https://api.proof.holdings');
        self::assertSame(
            $case['expected'],
            $proof->confirmations->verifyDecisionSignature($case['confirmation'], $case['public_key']),
        );
    }

    /**
     * The cases a `Confirmation` can hold (a non-string id or a non-array response cannot be one), each
     * with the members `fromArray` requires and the case does not need.
     *
     * @return array<string, array{array<string, mixed>, Confirmation}>
     */
    public static function retrievedCases(): array
    {
        $cases = [];
        foreach (self::cases() as $name => [$case]) {
            try {
                $cases[$name] = [$case, Confirmation::fromArray($case['confirmation'] + [
                    'hitl_id' => '6720a1b2c3d4e5f6a7b8c9f0',
                    'message' => '{}',
                    'created_at' => '2026-10-29T08:00:00.000Z',
                    'updated_at' => '2026-10-29T09:00:00.000Z',
                ])];
            } catch (\TypeError) {
                // Not representable as a Confirmation; the API-array test covers it.
            }
        }
        return $cases;
    }

    public function testMostCasesAreRepresentableAsAConfirmation(): void
    {
        self::assertGreaterThan(count(self::cases()) - 3, count(self::retrievedCases()));
    }

    /** @param array<string, mixed> $case */
    #[DataProvider('retrievedCases')]
    public function testCaseFromTheRetrievedConfirmation(array $case, Confirmation $confirmation): void
    {
        $proof = new Proof('pk_test_decision_signature', 'https://api.proof.holdings');
        self::assertSame(
            $case['expected'],
            $proof->confirmations->verifyDecisionSignature($confirmation, $case['public_key']),
        );
    }
}

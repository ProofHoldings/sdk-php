<?php

declare(strict_types=1);

namespace Proof\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proof\Polling;
use Proof\Exceptions\PollingTimeoutException;

class PollingTest extends TestCase
{
    public function testImmediateTerminal(): void
    {
        $result = Polling::waitUntilComplete(
            fn () => ['id' => 'ver_1', 'status' => 'verified'],
            ['verified', 'failed'],
            'Test',
            0.01,
            0.1,
        );

        $this->assertSame('verified', $result['status']);
    }

    public function testPollsUntilTerminal(): void
    {
        $callCount = 0;
        $result = Polling::waitUntilComplete(
            function () use (&$callCount) {
                $callCount++;
                return ['id' => 'ver_1', 'status' => $callCount >= 3 ? 'verified' : 'pending'];
            },
            ['verified', 'failed'],
            'Test',
            0.01,
            5.0,
        );

        $this->assertSame('verified', $result['status']);
        $this->assertGreaterThanOrEqual(3, $callCount);
    }

    public function testTimeout(): void
    {
        $this->expectException(PollingTimeoutException::class);

        Polling::waitUntilComplete(
            fn () => ['id' => 'ver_1', 'status' => 'pending'],
            ['verified', 'failed'],
            'Test',
            0.01,
            0.05,
        );
    }

    #[DataProvider('verificationTerminalStatesProvider')]
    public function testVerificationTerminalStates(string $status): void
    {
        $terminalStates = ['verified', 'failed', 'expired', 'revoked'];
        $result = Polling::waitUntilComplete(
            fn () => ['status' => $status],
            $terminalStates,
            'Test',
            0.01,
            0.1,
        );
        $this->assertSame($status, $result['status']);
    }

    public static function verificationTerminalStatesProvider(): array
    {
        return [
            'verified' => ['verified'],
            'failed' => ['failed'],
            'expired' => ['expired'],
            'revoked' => ['revoked'],
        ];
    }

    #[DataProvider('sessionTerminalStatesProvider')]
    public function testSessionTerminalStates(string $status): void
    {
        $terminalStates = ['verified', 'failed', 'expired'];
        $result = Polling::waitUntilComplete(
            fn () => ['status' => $status],
            $terminalStates,
            'Test',
            0.01,
            0.1,
        );
        $this->assertSame($status, $result['status']);
    }

    public static function sessionTerminalStatesProvider(): array
    {
        return [
            'verified' => ['verified'],
            'failed' => ['failed'],
            'expired' => ['expired'],
        ];
    }

    #[DataProvider('requestTerminalStatesProvider')]
    public function testRequestTerminalStates(string $status): void
    {
        $terminalStates = ['completed', 'expired', 'cancelled'];
        $result = Polling::waitUntilComplete(
            fn () => ['status' => $status],
            $terminalStates,
            'Test',
            0.01,
            0.1,
        );
        $this->assertSame($status, $result['status']);
    }

    public static function requestTerminalStatesProvider(): array
    {
        return [
            'completed' => ['completed'],
            'expired' => ['expired'],
            'cancelled' => ['cancelled'],
        ];
    }

    public function testPendingIsNotTerminal(): void
    {
        $this->expectException(PollingTimeoutException::class);

        Polling::waitUntilComplete(
            fn () => ['status' => 'pending'],
            ['verified', 'failed'],
            'Test',
            0.01,
            0.03,
        );
    }

    public function testDefaultConstants(): void
    {
        $this->assertSame(3.0, Polling::DEFAULT_INTERVAL);
        $this->assertSame(600.0, Polling::DEFAULT_TIMEOUT);
    }
}

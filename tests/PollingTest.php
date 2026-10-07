<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Polling;
use ProofHoldings\Exceptions\PollingCancelledException;
use ProofHoldings\Exceptions\PollingTimeoutException;

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
        $this->assertSame(2.0, Polling::DEFAULT_INTERVAL);
        $this->assertSame(600.0, Polling::DEFAULT_TIMEOUT);
        $this->assertSame(1.5, Polling::DEFAULT_BACKOFF);
        $this->assertSame(30.0, Polling::DEFAULT_MAX_INTERVAL);
        $this->assertSame(0.5, Polling::DEFAULT_JITTER);
    }

    public function testBackoffIncreasesInterval(): void
    {
        $callCount = 0;
        $callTimes = [];
        $result = Polling::waitUntilComplete(
            function () use (&$callCount, &$callTimes) {
                $callTimes[] = hrtime(true);
                $callCount++;
                return ['id' => 'ver_1', 'status' => $callCount >= 4 ? 'verified' : 'pending'];
            },
            ['verified', 'failed'],
            'Test',
            interval: 0.05,
            timeout: 10.0,
            backoff: 2.0,
            maxInterval: 1.0,
            jitter: 0.0,
        );

        $this->assertSame('verified', $result['status']);
        $this->assertCount(4, $callTimes);
        $gap1 = ($callTimes[1] - $callTimes[0]) / 1e9;
        $gap2 = ($callTimes[2] - $callTimes[1]) / 1e9;
        $this->assertGreaterThan($gap1, $gap2);
    }

    public function testCancellationAbortsPolling(): void
    {
        $this->expectException(PollingCancelledException::class);

        $state = (object) ['cancelled' => false, 'callCount' => 0];
        Polling::waitUntilComplete(
            function () use ($state) {
                $state->callCount++;
                if ($state->callCount >= 2) {
                    $state->cancelled = true;
                }
                return ['status' => 'pending'];
            },
            ['verified', 'failed'],
            'Test',
            interval: 0.05,
            timeout: 10.0,
            backoff: 1.0,
            maxInterval: 0.1,
            jitter: 0.0,
            shouldCancel: fn () => $state->cancelled,
        );
    }

    public function testCancellationPresetBeforeCall(): void
    {
        $this->expectException(PollingCancelledException::class);

        Polling::waitUntilComplete(
            fn () => ['status' => 'pending'],
            ['verified'],
            'Test',
            interval: 0.05,
            timeout: 10.0,
            shouldCancel: fn () => true,
        );
    }

    public function testMaxIntervalCapsBackoff(): void
    {
        $callCount = 0;
        $result = Polling::waitUntilComplete(
            function () use (&$callCount) {
                $callCount++;
                return ['status' => $callCount >= 5 ? 'verified' : 'pending'];
            },
            ['verified'],
            'Test',
            interval: 0.01,
            timeout: 10.0,
            backoff: 10.0,
            maxInterval: 0.05,
            jitter: 0.0,
        );
        $this->assertSame('verified', $result['status']);
    }
}

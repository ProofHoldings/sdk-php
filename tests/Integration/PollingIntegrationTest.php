<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use ProofHoldings\Exceptions\PollingCancelledException;
use ProofHoldings\Exceptions\PollingTimeoutException;
use ProofHoldings\Polling;

class PollingIntegrationTest extends IntegrationTestCase
{
    public function testReturnsOnTerminalState(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $calls = 0;
        $result = Polling::waitUntilComplete(
            retrieve: function () use (&$calls) {
                $calls++;
                return ['status' => $calls < 3 ? 'pending' : 'verified'];
            },
            terminalStates: ['verified', 'failed'],
            label: 'verification',
            interval: 0.005,
            timeout: 2.0,
            backoff: 1.0,
            maxInterval: 0.005,
            jitter: 0.0,
        );
        $this->assertSame('verified', $result['status']);
        $this->assertSame(3, $calls);
    }

    public function testTimeoutRaisesPollingTimeoutException(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $this->expectException(PollingTimeoutException::class);
        Polling::waitUntilComplete(
            retrieve: fn () => ['status' => 'pending'],
            terminalStates: ['verified'],
            label: 'verification',
            interval: 0.005,
            timeout: 0.02,
            backoff: 1.0,
            maxInterval: 0.005,
            jitter: 0.0,
        );
    }

    public function testShouldCancelCallbackAbortsPolling(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }

        $this->expectException(PollingCancelledException::class);
        // shouldCancel is checked at the top of each iteration AND inside the
        // sleep loop. Returning true immediately means we never enter the
        // retrieve call — exactly the "abort before work" semantic we want.
        Polling::waitUntilComplete(
            retrieve: fn () => ['status' => 'pending'],
            terminalStates: ['verified'],
            label: 'verification',
            interval: 0.005,
            timeout: 5.0,
            backoff: 1.0,
            maxInterval: 0.005,
            jitter: 0.0,
            shouldCancel: fn () => true,
        );
    }
}

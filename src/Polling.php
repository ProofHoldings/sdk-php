<?php

declare(strict_types=1);

namespace ProofHoldings;

use ProofHoldings\Exceptions\PollingCancelledException;
use ProofHoldings\Exceptions\PollingTimeoutException;

class Polling
{
    public const float DEFAULT_INTERVAL = 2.0;
    public const float DEFAULT_TIMEOUT = 600.0;
    public const float DEFAULT_BACKOFF = 1.5;
    public const float DEFAULT_MAX_INTERVAL = 30.0;
    public const float DEFAULT_JITTER = 0.5;
    private const int MICROSECONDS_PER_SECOND = 1_000_000;

    public const array VERIFICATION_TERMINAL_STATES = ['verified', 'failed', 'expired', 'revoked'];
    public const array SESSION_TERMINAL_STATES = ['verified', 'failed', 'expired'];
    public const array REQUEST_TERMINAL_STATES = ['completed', 'expired', 'cancelled'];

    /**
     * Poll a retrieve callback until the returned status is terminal or timeout is reached.
     *
     * Implements the standard polling contract (docs/POLLING_CONTRACT.md):
     * exponential backoff with jitter. PHP has no native AbortSignal, so pass
     * a ``shouldCancel`` callback that returns true to abort early; a
     * :class:`PollingCancelledException` is thrown when it does.
     *
     * @param callable(): array $retrieve    Callback that returns the current resource state
     * @param list<string>      $terminalStates Status values that end polling
     * @param string            $label       Resource label for error messages (e.g. "Verification ver_123")
     * @param float             $interval    Initial poll interval in seconds
     * @param float             $timeout     Timeout in seconds
     * @param float             $backoff     Multiplicative backoff factor applied after each poll
     * @param float             $maxInterval Maximum poll interval in seconds
     * @param float             $jitter      Maximum random jitter (seconds) added to each sleep
     * @param (callable(): bool)|null $shouldCancel Optional callback; returning true aborts polling
     */
    public static function waitUntilComplete(
        callable $retrieve,
        array $terminalStates,
        string $label,
        float $interval = self::DEFAULT_INTERVAL,
        float $timeout = self::DEFAULT_TIMEOUT,
        float $backoff = self::DEFAULT_BACKOFF,
        float $maxInterval = self::DEFAULT_MAX_INTERVAL,
        float $jitter = self::DEFAULT_JITTER,
        ?callable $shouldCancel = null,
    ): array {
        self::requirePositiveFinite('interval', $interval);
        self::requirePositiveFinite('timeout', $timeout);
        self::requirePositiveFinite('maxInterval', $maxInterval);
        if (!is_finite($backoff) || $backoff < 1) {
            throw new \InvalidArgumentException(
                sprintf('backoff must be a finite number >= 1, got %s', var_export($backoff, true))
            );
        }
        if (!is_finite($jitter) || $jitter < 0) {
            throw new \InvalidArgumentException(
                sprintf('jitter must be a finite non-negative number, got %s', var_export($jitter, true))
            );
        }

        $start = hrtime(true);
        $currentInterval = $interval;

        while (true) {
            if ($shouldCancel !== null && $shouldCancel()) {
                throw new PollingCancelledException("{$label} polling cancelled");
            }

            $resource = $retrieve();

            if (in_array($resource['status'] ?? '', $terminalStates, true)) {
                return $resource;
            }

            $elapsed = (hrtime(true) - $start) / 1e9;
            if ($elapsed >= $timeout) {
                $lastStatus = $resource['status'] ?? 'unknown';
                throw new PollingTimeoutException(
                    "{$label} did not complete within {$timeout}s (last status: {$lastStatus})"
                );
            }

            $jitterAmount = $jitter > 0 ? mt_rand() / mt_getrandmax() * $jitter : 0.0;
            $sleep = $currentInterval + $jitterAmount;
            self::sleepWithCancel($sleep, $shouldCancel, $label);

            $currentInterval = min($currentInterval * $backoff, $maxInterval);
        }
    }

    private static function requirePositiveFinite(string $name, float $value): void
    {
        if (!is_finite($value) || $value <= 0) {
            throw new \InvalidArgumentException(
                sprintf('%s must be a finite positive number, got %s', $name, var_export($value, true))
            );
        }
    }

    /**
     * Sleep for up to $seconds, checking $shouldCancel every 100ms. Throws
     * PollingCancelledException if the callback returns true during the sleep.
     */
    private static function sleepWithCancel(float $seconds, ?callable $shouldCancel, string $label): void
    {
        if ($shouldCancel === null) {
            usleep((int) ($seconds * self::MICROSECONDS_PER_SECOND));
            return;
        }

        $remaining = $seconds;
        $checkStep = 0.1;
        while ($remaining > 0) {
            if ($shouldCancel()) {
                throw new PollingCancelledException("{$label} polling cancelled");
            }
            $chunk = min($remaining, $checkStep);
            usleep((int) ($chunk * self::MICROSECONDS_PER_SECOND));
            $remaining -= $chunk;
        }
    }
}

<?php

declare(strict_types=1);

namespace Proof;

use Proof\Exceptions\PollingTimeoutException;

class Polling
{
    public const float DEFAULT_INTERVAL = 3.0;
    public const float DEFAULT_TIMEOUT = 600.0;
    private const int MICROSECONDS_PER_SECOND = 1_000_000;

    public const array VERIFICATION_TERMINAL_STATES = ['verified', 'failed', 'expired', 'revoked'];
    public const array SESSION_TERMINAL_STATES = ['verified', 'failed', 'expired'];
    public const array REQUEST_TERMINAL_STATES = ['completed', 'expired', 'cancelled'];

    /**
     * Poll a retrieve callback until the returned status is terminal or timeout is reached.
     *
     * @param callable(): array $retrieve  Callback that returns the current resource state
     * @param list<string>      $terminalStates  Status values that end polling
     * @param string            $label  Resource label for error messages (e.g. "Verification ver_123")
     * @param float             $interval  Poll interval in seconds
     * @param float             $timeout   Timeout in seconds
     */
    public static function waitUntilComplete(
        callable $retrieve,
        array $terminalStates,
        string $label,
        float $interval = self::DEFAULT_INTERVAL,
        float $timeout = self::DEFAULT_TIMEOUT,
    ): array {
        $start = hrtime(true);

        while (true) {
            $resource = $retrieve();

            if (in_array($resource['status'] ?? '', $terminalStates, true)) {
                return $resource;
            }

            $elapsed = (hrtime(true) - $start) / 1e9;
            if ($elapsed >= $timeout) {
                throw new PollingTimeoutException(
                    "{$label} did not complete within {$timeout}s (last status: {$resource['status']})"
                );
            }

            usleep((int) ($interval * self::MICROSECONDS_PER_SECOND));
        }
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings;

/**
 * The answer of {@see Credentials::resolveApiKey}: the key and where it came from
 * (`explicit` | `env` | `file` | `none`), or no key and, when the credentials file exists but
 * could not be used, a notice saying why — never a key.
 */
final class ResolvedKey
{
    public function __construct(
        public readonly ?string $apiKey,
        public readonly string $source,
        public readonly ?string $notice = null,
    ) {}
}

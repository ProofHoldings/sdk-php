<?php

declare(strict_types=1);

namespace ProofHoldings;

/**
 * Where the key comes from when `new Proof()` is given none.
 *
 * The chain is the MCP server's (`mcp/src/credentials.ts`, the reference): a non-empty
 * `PROOF_API_KEY`, else the entry `npx @proof-holdings/mcp-server login` saved for exactly this
 * base URL in `~/.proof-holdings/credentials`, else none. It runs ONLY when the key argument is
 * omitted — an explicit key is used as given, and an explicit empty one is the caller's error and
 * never reads the environment or the file (a multi-tenant host passing a tenant's key must not
 * fall through to its own). The shared cases in `sdks/shared/fixtures/credentials/cases.json`
 * hold this class and the other three SDKs to the reference.
 */
final class Credentials
{
    /** The scoped command, never the bare bin name: through npx the unscoped name resolves to a package somebody else publishes. */
    public const string LOGIN_COMMAND = 'npx @proof-holdings/mcp-server login';
    public const string DEFAULT_BASE_URL = 'https://api.proof.holdings';

    private const array DEFAULT_PORTS = ['http' => 80, 'https' => 443];

    /**
     * `~/.proof-holdings/credentials`, or null when no home directory can be found.
     *
     * The home directory comes from the variable the login's Node `os.homedir()` reads — and Python's
     * `Path.home()` and Go's `os.UserHomeDir()` — which is `USERPROFILE` on Windows and `HOME`
     * elsewhere. A Git Bash or MSYS shell on Windows sets both, and they differ: preferring `HOME`
     * there would read a different file than the login wrote.
     */
    public static function defaultPath(): ?string
    {
        $home = getenv(PHP_OS_FAMILY === 'Windows' ? 'USERPROFILE' : 'HOME');
        if ($home === false || $home === '') {
            return null;
        }
        return $home . DIRECTORY_SEPARATOR . '.proof-holdings' . DIRECTORY_SEPARATOR . 'credentials';
    }

    /**
     * Origin plus path, no trailing slash — the entry name the login saves under:
     * `https://API.Proof.Holdings:443/` → `https://api.proof.holdings`,
     * `http://localhost:3000/proxy/` → `http://localhost:3000/proxy`.
     *
     * @throws \InvalidArgumentException for anything without a scheme and a host
     */
    public static function credentialKey(string $baseUrl): string
    {
        $parts = parse_url($baseUrl);
        $scheme = is_array($parts) && isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        $host = is_array($parts) && isset($parts['host']) ? strtolower($parts['host']) : '';
        if ($scheme === '' || $host === '') {
            throw new \InvalidArgumentException('not a URL: ' . json_encode($baseUrl, JSON_UNESCAPED_SLASHES));
        }
        $origin = $scheme . '://' . $host;
        if (isset($parts['port']) && $parts['port'] !== (self::DEFAULT_PORTS[$scheme] ?? null)) {
            $origin .= ':' . $parts['port'];
        }
        return $origin . rtrim($parts['path'] ?? '', '/');
    }

    /**
     * One variable from `$env`, or from the process when `$env` is null — by NAME, as `getenv('HOME')`
     * above: the no-argument `getenv()` array is built from the process environ alone, so under
     * Apache `mod_php` it does not carry `SetEnv` values that `getenv('NAME')` does. Unset reads as ''.
     *
     * @param array<string, string>|null $env
     */
    private static function readEnv(?array $env, string $name): string
    {
        if ($env !== null) {
            return $env[$name] ?? '';
        }
        $value = getenv($name);
        return $value === false ? '' : $value;
    }

    /**
     * Explicit, else a non-empty `PROOF_BASE_URL`, else the default.
     *
     * @param array<string, string>|null $env the environment to read; the process's own when null
     */
    public static function resolveBaseUrl(?string $explicit = null, ?array $env = null): string
    {
        if ($explicit !== null) {
            return $explicit;
        }
        $fromEnv = self::readEnv($env, 'PROOF_BASE_URL');
        return $fromEnv !== '' ? $fromEnv : self::DEFAULT_BASE_URL;
    }

    /** @return array<string, string>|null the entry's fields, or null when it is not a stored credential */
    private static function asStoredCredential(mixed $value): ?array
    {
        if (!$value instanceof \stdClass) {
            return null;
        }
        $apiKey = $value->api_key ?? null;
        $keyId = $value->key_id ?? null;
        $name = $value->name ?? null;
        $environment = $value->environment ?? null;
        $createdAt = $value->created_at ?? null;
        if (
            !is_string($apiKey) || !str_starts_with($apiKey, 'pk_')
            || !is_string($keyId) || !is_string($name) || !is_string($createdAt)
            || ($environment !== 'production' && $environment !== 'test')
        ) {
            return null;
        }
        return [
            'api_key' => $apiKey,
            'key_id' => $keyId,
            'name' => $name,
            'environment' => $environment,
            'created_at' => $createdAt,
        ];
    }

    /**
     * The file's entries by base URL, or null when there is no file.
     *
     * @return array<string, array<string, string>>|null
     * @throws CredentialsFileException when the file is unreadable or malformed
     */
    public static function readFile(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new CredentialsFileException("cannot read {$path}");
        }
        $parsed = json_decode($raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CredentialsFileException("{$path} is not valid JSON");
        }
        if (
            !$parsed instanceof \stdClass
            || ($parsed->version ?? null) !== 1
            || !(($parsed->credentials ?? null) instanceof \stdClass)
        ) {
            throw new CredentialsFileException("{$path} is not a version 1 Proof credentials file");
        }
        $entries = [];
        foreach (get_object_vars($parsed->credentials) as $key => $value) {
            $entry = self::asStoredCredential($value);
            if ($entry === null) {
                throw new CredentialsFileException("{$path} has a malformed entry for {$key}");
            }
            $entries[$key] = $entry;
        }
        return $entries;
    }

    /**
     * The chain: an explicit key as given (the constructor decides whether `''` is acceptable),
     * else a non-empty `PROOF_API_KEY`, else the saved entry for exactly `$baseUrl`, else none.
     * A broken file or a base URL that is not a URL never throws here — it becomes a notice on
     * the `none` answer.
     *
     * @param string|null $path the credentials file; `~/.proof-holdings/credentials` when null
     * @param array<string, string>|null $env the environment to read; the process's own when null
     */
    public static function resolveApiKey(?string $explicit, string $baseUrl, ?string $path = null, ?array $env = null): ResolvedKey
    {
        if ($explicit !== null) {
            return new ResolvedKey($explicit, 'explicit');
        }
        $envKey = self::readEnv($env, 'PROOF_API_KEY');
        if ($envKey !== '') {
            return new ResolvedKey($envKey, 'env');
        }
        $path ??= self::defaultPath();
        if ($path === null) {
            // No home directory to look in: the same answer as no file.
            return new ResolvedKey(null, 'none');
        }
        try {
            $entries = self::readFile($path);
        } catch (CredentialsFileException $e) {
            return new ResolvedKey(
                null,
                'none',
                "ignoring the saved credentials — {$e->getMessage()}. Fix or delete {$path}, then run " . self::LOGIN_COMMAND . '.',
            );
        }
        if ($entries === null) {
            return new ResolvedKey(null, 'none');
        }
        try {
            $key = self::credentialKey($baseUrl);
        } catch (\InvalidArgumentException) {
            return new ResolvedKey(
                null,
                'none',
                'the base URL ' . json_encode($baseUrl, JSON_UNESCAPED_SLASHES) . " is not a URL, so the saved credentials in {$path} were not read.",
            );
        }
        $entry = $entries[$key] ?? null;
        return $entry === null ? new ResolvedKey(null, 'none') : new ResolvedKey($entry['api_key'], 'file');
    }

    /** The one message a missing key produces, whatever road the caller tried. */
    public static function missingKeyMessage(string $baseUrl, ?string $notice = null): string
    {
        $message = 'api_key is required. Pass your key as: new Proof("pk_live_..."), set PROOF_API_KEY, or run `'
            . self::LOGIN_COMMAND . '` to save one for ' . $baseUrl . '.';
        return $notice === null || $notice === '' ? $message : $message . ' ' . $notice;
    }
}

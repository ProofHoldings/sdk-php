<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Credentials;
use ProofHoldings\Proof;
use ProofHoldings\ResolvedKey;
use ProofHoldings\Version;
use ReflectionProperty;

/**
 * Key and base-URL resolution follows the MCP server's chain (m-implement-sdk-key-resolution).
 *
 * The shared cases in `sdks/shared/fixtures/credentials/cases.json` run here through the PUBLIC
 * surface — `new Proof($key)` or `new Proof(baseUrl: ...)` / `new Proof()` — with `HOME` pointed at a
 * temp directory holding the case's credentials file and the two variables set from the case. What
 * is asserted is what reaches the wire: the `Authorization` header Guzzle's history middleware records
 * and the origin it was sent to. The same file drives the MCP reference (through the root drift test)
 * and the other three SDKs, so a rule read differently here fails here.
 *
 * The origin, not the whole base URL, is compared: Guzzle resolves a relative path against the base
 * the RFC 3986 way, which drops a base path. That the path still took part in the FILE lookup is what
 * the `Authorization` assertion of `path_prefix_is_part_of_the_key` proves.
 */
final class CredentialsFixturesTest extends TestCase
{
    private const string FIXTURE = __DIR__ . '/../../shared/fixtures/credentials/cases.json';
    private const array ENV_KEYS = ['PROOF_API_KEY', 'PROOF_BASE_URL'];
    private const array DEFAULT_PORTS = ['http' => 80, 'https' => 443];

    /** The fixture's `files`, decoded to objects so `{}` survives a round trip. */
    private static \stdClass $files;

    private string $dir;

    /** @var array<string, string|false> */
    private array $savedEnv = [];

    public static function setUpBeforeClass(): void
    {
        $fixture = json_decode((string) file_get_contents(self::FIXTURE));
        self::$files = $fixture->files;
        self::assertSame(Credentials::LOGIN_COMMAND, $fixture->login_command);
    }

    /** @return iterable<string, array{array<string, mixed>}> */
    public static function cases(): iterable
    {
        $fixture = json_decode((string) file_get_contents(self::FIXTURE), true);
        foreach ($fixture['cases'] as $case) {
            yield $case['name'] => [$case];
        }
    }

    protected function setUp(): void
    {
        foreach (['HOME', 'USERPROFILE', ...self::ENV_KEYS] as $key) {
            $this->savedEnv[$key] = getenv($key);
        }
        $this->dir = sys_get_temp_dir() . '/proof-cred-' . bin2hex(random_bytes(4));
        mkdir($this->dir, 0700, true);
        putenv("HOME={$this->dir}");
    }

    protected function tearDown(): void
    {
        foreach ($this->savedEnv as $key => $value) {
            putenv($value === false ? $key : "{$key}={$value}");
        }
        self::removeDir($this->dir);
    }

    private static function removeDir(string $dir): void
    {
        foreach (scandir($dir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $dir . '/' . $entry;
            is_dir($path) ? self::removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    /** @param array<string, mixed> $case */
    private function arrange(array $case): string
    {
        $file = $this->dir . DIRECTORY_SEPARATOR . '.proof-holdings' . DIRECTORY_SEPARATOR . 'credentials';
        if ($case['file'] !== null) {
            mkdir(dirname($file), 0700, true);
            $body = self::$files->{$case['file']};
            file_put_contents($file, is_string($body) ? $body : json_encode($body));
        }
        $env = $case['env'] ?? [];
        foreach (self::ENV_KEYS as $key) {
            putenv(array_key_exists($key, $env) ? "{$key}={$env[$key]}" : $key);
        }
        return $file;
    }

    /** @param array<string, mixed> $case */
    private static function build(array $case): Proof
    {
        $baseUrl = $case['base_url'];
        if (array_key_exists('explicit', $case)) {
            return $baseUrl === null ? new Proof($case['explicit']) : new Proof($case['explicit'], $baseUrl);
        }
        return $baseUrl === null ? new Proof() : new Proof(baseUrl: $baseUrl);
    }

    /**
     * The `IntegrationTestCase::makeClient` swap: the SDK's own base URL and headers (the key read
     * back from the SDK, which is the fact under test), a mock handler and a history recorder.
     *
     * @param array<int, array<string, mixed>> $history
     */
    private static function routeToRecorder(Proof $proof, array &$history): void
    {
        $http = (new ReflectionProperty($proof->verifications, 'http'))->getValue($proof->verifications);
        $apiKey = (new ReflectionProperty($http, 'apiKey'))->getValue($http);
        $stack = HandlerStack::create(new MockHandler([new Response(200, ['Content-Type' => 'application/json'], '{}')]));
        $stack->push(Middleware::history($history));
        $guzzle = new Client([
            'base_uri' => rtrim($http->baseUrl, '/'),
            'handler' => $stack,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
            ],
        ]);
        (new ReflectionProperty($http, 'client'))->setValue($http, $guzzle);
    }

    /** Scheme, lowercased host and effective port — what every runner compares. */
    private static function origin(string $url): string
    {
        $parts = parse_url($url);
        self::assertIsArray($parts);
        $scheme = strtolower($parts['scheme']);
        $port = $parts['port'] ?? self::DEFAULT_PORTS[$scheme];
        return $scheme . '://' . strtolower($parts['host']) . ':' . $port;
    }

    /** @param array<string, mixed> $case */
    #[DataProvider('cases')]
    public function testSharedCase(array $case): void
    {
        $file = $this->arrange($case);

        if ($case['expect']['error']) {
            try {
                self::build($case);
            } catch (\InvalidArgumentException $e) {
                $message = $e->getMessage();
                $this->assertStringContainsString('api_key is required', $message);
                if (array_key_exists('explicit', $case)) {
                    // Today's error, verbatim: the chain was never consulted.
                    $this->assertStringNotContainsString(Credentials::LOGIN_COMMAND, $message);
                } else {
                    $this->assertStringContainsString(Credentials::LOGIN_COMMAND, $message);
                    $this->assertStringContainsString('PROOF_API_KEY', $message);
                }
                $this->assertSame($case['expect']['notice'], str_contains($message, $file));
                return;
            }
            $this->fail('expected construction to fail');
        }

        $proof = self::build($case);
        $history = [];
        self::routeToRecorder($proof, $history);
        $proof->verifications->retrieve('ver_x');
        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertSame('Bearer ' . $case['expect']['api_key'], $request->getHeaderLine('Authorization'));
        $this->assertSame(self::origin($case['expect']['base_url']), self::origin((string) $request->getUri()));
    }

    private function validFile(): string
    {
        $file = $this->dir . '/credentials';
        file_put_contents($file, json_encode(self::$files->valid));
        return $file;
    }

    public function testExplicitIsUsedAsGivenEvenEmpty(): void
    {
        $file = $this->dir . '/credentials';
        file_put_contents($file, '{not json');
        $env = ['PROOF_API_KEY' => 'pk_live_env'];
        $this->assertEquals(new ResolvedKey('pk_live_x', 'explicit'), Credentials::resolveApiKey('pk_live_x', Credentials::DEFAULT_BASE_URL, $file, $env));
        $this->assertEquals(new ResolvedKey('', 'explicit'), Credentials::resolveApiKey('', Credentials::DEFAULT_BASE_URL, $file, $env));
    }

    public function testEnvWinsWithoutReadingTheFile(): void
    {
        $file = $this->dir . '/credentials';
        file_put_contents($file, '{not json');
        $result = Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, $file, ['PROOF_API_KEY' => 'pk_live_env']);
        $this->assertEquals(new ResolvedKey('pk_live_env', 'env'), $result);
    }

    public function testFileEntryForExactlyThisBaseUrl(): void
    {
        $file = $this->validFile();
        $this->assertEquals(
            new ResolvedKey('pk_live_file_production', 'file'),
            Credentials::resolveApiKey(null, 'https://API.Proof.Holdings:443/', $file, []),
        );
        $this->assertEquals(
            new ResolvedKey('pk_test_file_proxy', 'file'),
            Credentials::resolveApiKey(null, 'http://localhost:3000/proxy/', $file, ['PROOF_API_KEY' => '']),
        );
    }

    public function testNoneIsSilentWithoutAFileAndANoticeWhenItCannotBeUsed(): void
    {
        $missing = $this->dir . '/credentials';
        $this->assertEquals(new ResolvedKey(null, 'none'), Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, $missing, []));

        $file = $this->validFile();
        $this->assertEquals(new ResolvedKey(null, 'none'), Credentials::resolveApiKey(null, 'https://api.staging.proof.holdings', $file, []));

        $broken = Credentials::resolveApiKey(null, 'not a url', $file, []);
        $this->assertSame('none', $broken->source);
        $this->assertNull($broken->apiKey);
        $this->assertStringContainsString($file, (string) $broken->notice);

        file_put_contents($file, '{"version": 2, "credentials": {}}');
        $wrongVersion = Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, $file, []);
        $this->assertSame('none', $wrongVersion->source);
        $this->assertStringContainsString($file, (string) $wrongVersion->notice);
        $this->assertStringContainsString(Credentials::LOGIN_COMMAND, (string) $wrongVersion->notice);
    }

    public function testProcessEnvironmentIsReadByName(): void
    {
        // A null $env reads the process by name (getenv('NAME')), the form that also sees SAPI
        // variables such as Apache SetEnv; the array form getenv() would not.
        putenv('PROOF_API_KEY=pk_live_from_process');
        putenv('PROOF_BASE_URL=https://api.staging.proof.holdings');
        $missing = $this->dir . '/credentials';
        $this->assertEquals(new ResolvedKey('pk_live_from_process', 'env'), Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, $missing, null));
        $this->assertSame('https://api.staging.proof.holdings', Credentials::resolveBaseUrl(null, null));
        putenv('PROOF_API_KEY');
        putenv('PROOF_BASE_URL=');
        $this->assertEquals(new ResolvedKey(null, 'none'), Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, $missing, null));
        $this->assertSame(Credentials::DEFAULT_BASE_URL, Credentials::resolveBaseUrl(null, null));
    }

    public function testNoHomeDirectoryIsTheSameAsNoFile(): void
    {
        putenv('HOME');
        putenv('USERPROFILE');
        $this->assertNull(Credentials::defaultPath());
        $this->assertEquals(new ResolvedKey(null, 'none'), Credentials::resolveApiKey(null, Credentials::DEFAULT_BASE_URL, null, []));
    }

    public function testCredentialKeyNormalizesLikeTheReference(): void
    {
        $this->assertSame('https://api.proof.holdings', Credentials::credentialKey('https://api.proof.holdings'));
        $this->assertSame('https://api.proof.holdings', Credentials::credentialKey('https://api.proof.holdings/'));
        $this->assertSame('https://api.proof.holdings', Credentials::credentialKey('https://API.Proof.Holdings:443/'));
        $this->assertSame('http://localhost:3000/proxy', Credentials::credentialKey('http://localhost:3000/proxy/'));
        $this->assertSame('http://localhost:8080', Credentials::credentialKey('http://localhost:8080'));
        $this->expectException(\InvalidArgumentException::class);
        Credentials::credentialKey('not a url');
    }

    public function testResolveBaseUrl(): void
    {
        $staging = 'https://api.staging.proof.holdings';
        $this->assertSame('http://localhost:3000', Credentials::resolveBaseUrl('http://localhost:3000', ['PROOF_BASE_URL' => $staging]));
        $this->assertSame($staging, Credentials::resolveBaseUrl(null, ['PROOF_BASE_URL' => $staging]));
        $this->assertSame(Credentials::DEFAULT_BASE_URL, Credentials::resolveBaseUrl(null, ['PROOF_BASE_URL' => '']));
        $this->assertSame(Credentials::DEFAULT_BASE_URL, Credentials::resolveBaseUrl(null, []));
    }
}

<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Proof;
use ProofHoldings\Version;
use ReflectionProperty;

/**
 * Base class for the PHP SDK integration suite.
 *
 * Same dual-mode approach as the JS/Python suites:
 *   - Live mode:  PROOF_BASE_URL + PROOF_API_KEY_TEST set → hit staging
 *   - Mock mode:  otherwise → inject Guzzle MockHandler via reflection
 *
 * Mirrors the Go SDK integration scope: client + resources + errors + polling
 * + streaming parser.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static function isLiveMode(): bool
    {
        return !empty(getenv('PROOF_BASE_URL')) && !empty(getenv('PROOF_API_KEY_TEST'));
    }

    /**
     * Cloudflare Access service-token headers, if the credentials are in env.
     * Used to traverse the Access perimeter fronting staging. Test-only — the
     * SDK production client knows nothing about CF Access.
     *
     * @return array<string, string>
     */
    public static function cfAccessHeaders(): array
    {
        $headers = [];
        $id = getenv('CF_ACCESS_CLIENT_ID');
        $secret = getenv('CF_ACCESS_CLIENT_SECRET');
        if (!empty($id) && !empty($secret)) {
            $headers['CF-Access-Client-Id'] = $id;
            $headers['CF-Access-Client-Secret'] = $secret;
        }
        $visualSecret = getenv('VISUAL_TEST_SECRET');
        if (!empty($visualSecret)) {
            $headers['X-Visual-Test-Secret'] = $visualSecret;
        }
        return $headers;
    }

    /**
     * Create a Proof client.
     *
     * - Live mode: swap the SDK's internal Guzzle client with one that
     *   includes CF Access service-token headers when available, so requests
     *   actually reach the API origin behind Cloudflare Access.
     * - Mock mode: swap the internal Guzzle client so the provided
     *   MockHandler drives every HTTP call.
     *
     * @param  Response[]  $responses  Queued mock responses (ignored in live mode).
     * @param  array<int, array{request: \Psr\Http\Message\RequestInterface, response: ?\Psr\Http\Message\ResponseInterface}>|null  $history
     *         When passed by reference, receives every request/response pair sent through the
     *         mock transport (Guzzle `Middleware::history()`) — use to assert on request bodies.
     *         Ignored in live mode.
     */
    protected function makeClient(array $responses = [], ?array &$history = null): Proof
    {
        $apiKey = getenv('PROOF_API_KEY_TEST') ?: 'pk_test_integration';
        $baseUrl = getenv('PROOF_BASE_URL') ?: 'https://mock.proof.test';

        $proof = new Proof($apiKey, $baseUrl, 15.0, 0);

        if (self::isLiveMode()) {
            $access = self::cfAccessHeaders();
            if (!empty($access)) {
                $resourceHttp = $this->readPrivate($proof->verifications, 'http');
                $proofsHttp = $this->readPrivate($proof->proofs, 'http');
                $this->assertSame(
                    $resourceHttp,
                    $proofsHttp,
                    'Proof::verifications and Proof::proofs must share a single HttpClient — live-mode header injection assumes it.',
                );
                $guzzle = new Client([
                    'base_uri' => rtrim($baseUrl, '/'),
                    'timeout' => 15.0,
                    'headers' => array_merge(
                        [
                            'Authorization' => "Bearer {$apiKey}",
                            'Content-Type' => 'application/json',
                            'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
                        ],
                        $access,
                    ),
                ]);
                $this->writePrivate($resourceHttp, 'client', $guzzle);
            }
            return $proof;
        }

        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        if ($history !== null) {
            $history = [];
            $stack->push(Middleware::history($history));
        }
        $guzzle = new Client([
            'base_uri' => rtrim($baseUrl, '/'),
            'timeout' => 15.0,
            'handler' => $stack,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'User-Agent' => 'proof-sdk-php/' . Version::VERSION,
            ],
        ]);

        // The HttpClient instance lives behind each resource; reach into
        // Verifications.http (or any resource), then swap the Guzzle
        // Client on that HttpClient so every subsequent call routes
        // through MockHandler. Verify the shared-HttpClient assumption
        // so a future SDK refactor giving each resource its own instance
        // fails fast rather than silently leaving non-verifications
        // resources talking to the real network in mock mode.
        $resourceHttp = $this->readPrivate($proof->verifications, 'http');
        $proofsHttp = $this->readPrivate($proof->proofs, 'http');
        $this->assertSame(
            $resourceHttp,
            $proofsHttp,
            'Proof::verifications and Proof::proofs must share a single HttpClient — mock transport swap assumes it.',
        );
        $this->writePrivate($resourceHttp, 'client', $guzzle);

        return $proof;
    }

    /** Read a private/protected property via reflection, with a clear message if the SDK renamed it. */
    protected function readPrivate(object $obj, string $name): mixed
    {
        $this->assertObjectHasPrivateProperty($obj, $name);
        return (new ReflectionProperty($obj, $name))->getValue($obj);
    }

    /** Write a private/protected property via reflection, with a clear message if the SDK renamed it. */
    protected function writePrivate(object $obj, string $name, mixed $value): void
    {
        $this->assertObjectHasPrivateProperty($obj, $name);
        (new ReflectionProperty($obj, $name))->setValue($obj, $value);
    }

    private function assertObjectHasPrivateProperty(object $obj, string $name): void
    {
        if (!(new \ReflectionClass($obj))->hasProperty($name)) {
            $this->fail(sprintf(
                "SDK internal '%s::%s' was renamed or removed — update IntegrationTestCase mock-transport seam.",
                $obj::class,
                $name,
            ));
        }
    }

    protected function jsonResponse(int $status, array $body, array $headers = []): Response
    {
        return new Response(
            $status,
            array_merge(['Content-Type' => 'application/json'], $headers),
            (string) json_encode($body),
        );
    }

    /**
     * Load a shared wire fixture from ``sdks/shared/fixtures/`` and strip
     * metadata keys (those starting with underscore). The canonical
     * validator lives in ``src/__tests__/drift/sdk-fixtures.test.ts``.
     *
     * @return array<string, mixed>
     */
    protected static function sharedFixture(string $name): array
    {
        $path = dirname(__DIR__, 3) . '/shared/fixtures/' . $name;
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("missing shared fixture: {$path}");
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("invalid shared fixture JSON: {$path}");
        }
        $clean = [];
        foreach ($decoded as $k => $v) {
            if (!str_starts_with((string) $k, '_')) {
                $clean[$k] = $v;
            }
        }
        return $clean;
    }
}

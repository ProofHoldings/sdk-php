<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;

/**
 * `HttpClient::fetchPublic` over a REAL socket (h-sdk-offline-revocation, review round 2).
 *
 * The rest of the offline-verification suite drives a Guzzle `MockHandler`, whose response body
 * lives in `php://temp` — and there a single `read($n)` returns the whole `$n`. Over a streamed
 * socket it returns whatever one packet carried, measured at 8192 bytes of a 100000-byte
 * document. That difference silently truncated every public read (the status list AND the key
 * set), and no mock-backed case could see it: the truncated list simply failed to parse, so the
 * verification still answered `status_unavailable` and every assertion stayed green.
 *
 * So this file speaks over a real socket. It is the only PHP case that does, and it exists for
 * that one reason.
 */
class PublicFetchSocketTest extends TestCase
{
    /** @var resource|null */
    private $server = null;
    private string $baseUrl = '';

    protected function setUp(): void
    {
        $port = $this->freePort();
        $router = escapeshellarg(__DIR__ . '/fixtures/socket-router.php');
        $command = sprintf('exec php -S 127.0.0.1:%d %s', $port, $router);

        $pipes = [];
        $process = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes
        );
        if (!is_resource($process)) {
            // FAIL, not skip: PHP CLI is already running this suite, so a server that will not
            // start is a broken environment, and a guard that quietly skips is a guard nobody
            // notices is gone. The suite already reports 11 legitimate skips, so one more would
            // have read as normal.
            self::fail('could not start the built-in PHP server');
        }
        $this->server = $process;
        $this->baseUrl = "http://127.0.0.1:{$port}";

        $this->waitUntilListening($port);
    }

    protected function tearDown(): void
    {
        if (is_resource($this->server)) {
            proc_terminate($this->server);
            proc_close($this->server);
            $this->server = null;
        }
    }

    private function freePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        self::assertIsResource($socket, "could not reserve a port: {$errstr}");
        $name = stream_socket_get_name($socket, false);
        fclose($socket);

        return (int) substr((string) $name, strrpos((string) $name, ':') + 1);
    }

    private function waitUntilListening(int $port): void
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $probe = @stream_socket_client("tcp://127.0.0.1:{$port}", $errno, $errstr, 0.1);
            if (is_resource($probe)) {
                fclose($probe);
                return;
            }
            usleep(50_000);
        }
        self::fail('the built-in PHP server never started listening');
    }

    public function testReadsABodyLongerThanOnePacketInFull(): void
    {
        $http = new HttpClient('pk_test_123', $this->baseUrl, 5.0, 0);

        $response = $http->fetchPublic("{$this->baseUrl}/body?size=100000", 5.0);

        $this->assertSame(200, $response['status']);
        // The defect this case exists for returned 8192 here.
        $this->assertSame(100000, strlen($response['body']));
        $this->assertSame(100000, $response['declared_length']);
    }

    public function testStopsOneByteOverTheCeiling(): void
    {
        $http = new HttpClient('pk_test_123', $this->baseUrl, 5.0, 0);

        $response = $http->fetchPublic("{$this->baseUrl}/body?size=2000000&declare=0", 5.0, [], 524288);

        // One byte past the ceiling is enough to know the body is oversized, and it is all that
        // is held in memory — a 2 MB document does not become a 2 MB allocation.
        $this->assertSame(524289, strlen($response['body']));
        $this->assertNull($response['declared_length']);
    }

    public function testDoesNotHangOnATransferThatNeverFinishes(): void
    {
        // What this case can honestly claim: a dripping server does not hold the call open.
        // MEASURED on this stand, the first thing to fire is Guzzle's own per-read timeout
        // (1.01s for a body arriving 50ms at a time), not the wall-clock deadline in
        // `readCapped` — so the deadline is the SECOND line of defence here and is pinned
        // directly, without a socket, in HttpClientDeadlineTest.
        $http = new HttpClient('pk_test_123', $this->baseUrl, 5.0, 0);

        $startedAt = microtime(true);
        try {
            $http->fetchPublic("{$this->baseUrl}/drip?bytes=200&pause=50000", 1.0);
            $this->fail('expected the read to give up');
        } catch (\Throwable $e) {
            $this->assertLessThan(5.0, microtime(true) - $startedAt, 'the call outlived its budget');
        }
    }

    public function testTheKeySetIsReadWithoutACredentialAndWithTheBrandedUserAgent(): void
    {
        // The key set now goes through the same credential-free client as the status list. The
        // server here refuses any other shape of request, so `unknown_key` — an empty key set read
        // and searched — is only reachable through the SDK's own client and headers.
        $token = implode('.', [
            rtrim(strtr(base64_encode('{"alg":"ES256","kid":"k1"}'), '+/', '-_'), '='),
            rtrim(strtr(base64_encode('{"iss":"proof.holdings"}'), '+/', '-_'), '='),
            'c2ln',
        ]);
        $http = new HttpClient('pk_test_123', $this->baseUrl, 5.0, 0);

        $result = (new \ProofHoldings\Resources\Proofs($http))->verifyOffline($token, false);

        $this->assertSame('unknown_key', $result['reason'], $result['error'] ?? '');

        // Positive control: the same read carrying a credential is refused by this server.
        $refused = $http->fetchPublic("{$this->baseUrl}/.well-known/jwks.json", 5.0, ['Authorization' => 'Bearer pk_test_123']);
        $this->assertSame(400, $refused['status']);
    }

    public function testAKeySetRedirectIsRefused(): void
    {
        // Followed, the redirect would land on the key set the test above reads (`unknown_key`).
        $token = implode('.', [
            rtrim(strtr(base64_encode('{"alg":"ES256","kid":"k1"}'), '+/', '-_'), '='),
            rtrim(strtr(base64_encode('{"iss":"proof.holdings"}'), '+/', '-_'), '='),
            'c2ln',
        ]);
        $http = new HttpClient('pk_test_123', "{$this->baseUrl}/redirecting", 5.0, 0);

        $result = (new \ProofHoldings\Resources\Proofs($http))->verifyOffline($token, false);

        $this->assertSame('status_unavailable', $result['reason']);
        $this->assertStringContainsString('HTTP 302', $result['error']);
    }
}

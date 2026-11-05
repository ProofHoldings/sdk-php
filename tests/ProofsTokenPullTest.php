<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Proofs;
use Psr\Http\Message\RequestInterface;
use ReflectionProperty;

/**
 * The token-pull calls on the proofs resource. A real HttpClient over a Guzzle MockHandler,
 * because Proofs reads the client's readonly baseUrl in its constructor, which a plain mock
 * never sets.
 */
class ProofsTokenPullTest extends TestCase
{
    private const CTL = 'ph_ctl_0123456789abcdef0123456789abcdef';

    /** @var array<int, array{request: RequestInterface}> */
    private array $history = [];

    private function proofs(array $body): Proofs
    {
        $http = new HttpClient('pk_test_123', 'https://mock.proof.test', 5.0, 0);
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
        ]));
        $this->history = [];
        $stack->push(Middleware::history($this->history));
        (new ReflectionProperty(HttpClient::class, 'client'))->setValue($http, new Client(['handler' => $stack]));
        return new Proofs($http);
    }

    private function sent(): RequestInterface
    {
        $this->assertCount(1, $this->history);
        return $this->history[0]['request'];
    }

    public function testTokenGetsEncodedHandleAndDecodesBody(): void
    {
        $pulled = [
            'proof_id' => self::CTL,
            'token' => 'eyJ.re-signed.token',
            'status' => 'active',
            'reissued_at' => null,
            'expires_at' => '2027-10-10T08:00:00.000Z',
        ];
        $proofs = $this->proofs($pulled);

        $result = $proofs->token('ph_ctl/special&id');

        $request = $this->sent();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/proofs/ph_ctl%2Fspecial%26id/token', $request->getUri()->getPath());
        $this->assertSame($pulled, $result);
    }

    public function testListReissuedSerializesDateTimeWithOffset(): void
    {
        $listed = [
            'data' => [[
                'proof_id' => self::CTL,
                'reissued_at' => '2026-10-10T08:00:00.000Z',
                'token_url' => '/api/v1/proofs/' . self::CTL . '/token',
            ]],
            'pagination' => ['page' => 2, 'limit' => 50, 'total' => 51, 'total_pages' => 2],
        ];
        $proofs = $this->proofs($listed);
        $since = new DateTimeImmutable('2026-10-01 15:30:00', new DateTimeZone('+03:00'));

        $result = $proofs->listReissued($since, ['page' => 2, 'limit' => 50]);

        $request = $this->sent();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/proofs', $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(
            ['reissued_since' => '2026-10-01T15:30:00.000+03:00', 'page' => '2', 'limit' => '50'],
            $query,
        );
        $this->assertStringContainsString('%2B03%3A00', $request->getUri()->getQuery());
        $this->assertSame($listed, $result);
    }

    public function testListReissuedPassesStringThrough(): void
    {
        $proofs = $this->proofs(['data' => [], 'pagination' => []]);

        $proofs->listReissued('2026-10-01T12:30:00Z');

        parse_str($this->sent()->getUri()->getQuery(), $query);
        $this->assertSame(['reissued_since' => '2026-10-01T12:30:00Z'], $query);
    }

    public function testDelegationTokenGetsEncodedPublicPull(): void
    {
        $proofs = $this->proofs(['token' => 'eyJ.delegation.token']);

        $result = $proofs->delegationToken('ph_dlg/special&id');

        $request = $this->sent();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/api/v1/proofs/delegation-token/ph_dlg%2Fspecial%26id', $request->getUri()->getPath());
        $this->assertSame(['token' => 'eyJ.delegation.token'], $result);
    }
}

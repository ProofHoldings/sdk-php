<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ProofHoldings\Resources\Proofs;
use ReflectionProperty;

class ProofsTest extends TestCase
{
    private const string PH_CTL = 'ph_ctl_bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb';

    /** @var array<int, array{request: \Psr\Http\Message\RequestInterface}> */
    private array $history = [];

    private function proofs(): Proofs
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, ['Content-Type' => 'application/json'], '{"status":"revoked"}')]));
        $stack->push(Middleware::history($this->history));
        $http = new HttpClient('pk_test_123', 'https://mock.proof.test', 5.0, 0);
        (new ReflectionProperty(HttpClient::class, 'client'))->setValue($http, new Client(['handler' => $stack]));
        return new Proofs($http);
    }

    public function testRevokeWithoutReasonSendsNoBody(): void
    {
        $this->proofs()->revoke(self::PH_CTL);

        $this->assertCount(1, $this->history);
        $request = $this->history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/api/v1/proofs/' . self::PH_CTL . '/revoke', $request->getUri()->getPath());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testRevokeSendsReason(): void
    {
        $this->proofs()->revoke(self::PH_CTL, 'key_compromise');

        $this->assertCount(1, $this->history);
        $this->assertSame('{"reason":"key_compromise"}', (string) $this->history[0]['request']->getBody());
    }
}

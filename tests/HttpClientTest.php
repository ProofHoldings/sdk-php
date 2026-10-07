<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\HttpClient;
use ReflectionProperty;

/**
 * Unit tests for HttpClient's response handling — specifically the
 * non-JSON success envelope ({data, content_type}) introduced for binary
 * endpoints like the public profile avatar.
 */
class HttpClientTest extends TestCase
{
    /** @param Response[] $responses */
    private function makeHttpClient(array $responses): HttpClient
    {
        $http = new HttpClient('pk_test_123', 'https://mock.proof.test', 5.0, 0);
        $mock = new MockHandler($responses);
        $guzzle = new Client(['handler' => HandlerStack::create($mock)]);
        $prop = new ReflectionProperty(HttpClient::class, 'client');
        $prop->setValue($http, $guzzle);
        return $http;
    }

    public function testBinarySuccessReturnsEnvelope(): void
    {
        $png = "\x89PNG\r\n";
        $http = $this->makeHttpClient([new Response(200, ['Content-Type' => 'image/png'], $png)]);

        $result = $http->get('/api/v1/profiles/p/pr_1/avatar');

        $this->assertSame('image/png', $result['content_type']);
        $this->assertSame($png, $result['data']);
    }

    public function testMissingContentTypeKeepsJsonPath(): void
    {
        $http = $this->makeHttpClient([new Response(200, [], '{"id":"x_1"}')]);

        $result = $http->get('/api/v1/whatever');

        $this->assertSame(['id' => 'x_1'], $result);
    }

    public function testJsonContentTypeDecodesBody(): void
    {
        $http = $this->makeHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"x_2"}'),
        ]);

        $result = $http->get('/api/v1/whatever');

        $this->assertSame(['id' => 'x_2'], $result);
    }

    public function testNonJsonEmptyBodyReturnsEmptyArray(): void
    {
        $http = $this->makeHttpClient([new Response(200, ['Content-Type' => 'text/plain'], '')]);

        $result = $http->get('/api/v1/some/endpoint');

        $this->assertSame([], $result);
    }
}

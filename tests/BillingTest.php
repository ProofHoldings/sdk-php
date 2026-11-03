<?php

declare(strict_types=1);

namespace ProofHoldings\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ProofHoldings\Exceptions\BalanceEmptyException;
use ProofHoldings\Exceptions\BillingUnavailableException;
use ProofHoldings\Exceptions\ProofException;
use ProofHoldings\Exceptions\ServerException;
use ProofHoldings\HttpClient;
use ProofHoldings\Proof;
use ProofHoldings\Resources\Billing;
use ProofHoldings\Types\CheckoutResponse;
use ProofHoldings\Types\PortalResponse;
use ReflectionProperty;

/**
 * The Billing resource, and the two billing refusals as typed exceptions — the 402 driven by the shared
 * `error_balance_empty.json`, the balance read by the shared `balance.json`.
 */
class BillingTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/../../shared/fixtures/';

    /** @return array<string, mixed> a shared fixture with its `_`-prefixed metadata keys dropped */
    private static function fixture(string $name): array
    {
        $raw = json_decode((string) file_get_contents(self::FIXTURES . $name), true, flags: JSON_THROW_ON_ERROR);
        return array_filter($raw, fn(string $key) => !str_starts_with($key, '_'), ARRAY_FILTER_USE_KEY);
    }

    private function mockHttp(): HttpClient
    {
        return $this->createMock(HttpClient::class);
    }

    /**
     * @param Response[] $responses
     * @return array{HttpClient, MockHandler}
     */
    private function answering(array $responses, int $maxRetries): array
    {
        $http = new HttpClient('pk_test_123', 'https://mock.proof.test', 5.0, $maxRetries);
        $mock = new MockHandler($responses);
        $prop = new ReflectionProperty(HttpClient::class, 'client');
        $prop->setValue($http, new Client(['handler' => HandlerStack::create($mock)]));
        return [$http, $mock];
    }

    public function testBillingIsWiredIntoTheClient(): void
    {
        $this->assertInstanceOf(Billing::class, (new Proof('pk_test_123'))->billing);
    }

    public function testSubscription(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())->method('get')->with('/api/v1/billing/subscription')
            ->willReturn(['plan' => 'free', 'status' => null]);
        $this->assertNull((new Billing($http))->subscription()['status']);
    }

    public function testCheckout(): void
    {
        $params = ['plan' => 'pro', 'success_url' => 'https://proof.holdings/billing?ok=1', 'cancel_url' => 'https://proof.holdings/billing'];
        $http = $this->mockHttp();
        $http->expects($this->once())->method('post')->with('/api/v1/billing/checkout', $params)
            ->willReturn(['checkout_url' => 'https://checkout.stripe.com/c/pay/cs_test']);
        $this->assertSame('https://checkout.stripe.com/c/pay/cs_test', (new Billing($http))->checkout($params)['checkout_url']);
    }

    public function testPortal(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())->method('post')->with('/api/v1/billing/portal', ['return_url' => 'https://proof.holdings/billing'])
            ->willReturn(['portal_url' => 'https://billing.stripe.com/p/session/test']);
        $result = (new Billing($http))->portal(['return_url' => 'https://proof.holdings/billing']);
        $this->assertSame('https://billing.stripe.com/p/session/test', $result['portal_url']);
    }

    public function testTopUpSendsANumericAmount(): void
    {
        $params = ['amount' => 50, 'success_url' => 'https://proof.holdings/usage?top_up=success', 'cancel_url' => 'https://proof.holdings/usage'];
        $http = $this->mockHttp();
        $http->expects($this->once())->method('post')->with('/api/v1/billing/top-up', $this->identicalTo($params))
            ->willReturn(['checkout_url' => 'https://checkout.stripe.com/c/pay/cs_top_up']);
        (new Billing($http))->topUp($params);
    }

    public function testBalanceReadsTheSharedFixtureShape(): void
    {
        $balance = self::fixture('balance.json');
        $http = $this->mockHttp();
        $http->expects($this->once())->method('get')->with('/api/v1/billing/balance')->willReturn($balance);
        $result = (new Billing($http))->balance();
        $this->assertSame('eur', $result['balance']['currency']);
        $this->assertTrue($result['auto_top_up']['has_payment_method']);
    }

    public function testBalanceLedgerSendsPageAndLimit(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())->method('get')->with('/api/v1/billing/balance/ledger', ['page' => 2, 'limit' => 10])
            ->willReturn(['data' => [], 'pagination' => ['page' => 2, 'limit' => 10, 'total' => 0, 'total_pages' => 1]]);
        (new Billing($http))->balanceLedger(['page' => 2, 'limit' => 10]);
    }

    public function testAutoTopUpPatches(): void
    {
        $http = $this->mockHttp();
        $http->expects($this->once())->method('patch')->with('/api/v1/billing/auto-top-up', ['enabled' => true, 'amount' => 50])
            ->willReturn(self::fixture('balance.json'));
        $this->assertSame(50, (new Billing($http))->autoTopUp(['enabled' => true, 'amount' => 50])['auto_top_up']['amount']);
    }

    public function testA402RaisesBalanceEmptyExceptionWithTheFourFields(): void
    {
        $body = self::fixture('error_balance_empty.json');
        [$http, $mock] = $this->answering([new Response(402, ['Content-Type' => 'application/json'], json_encode($body))], 2);
        try {
            $http->post('/api/v1/verifications', []);
            $this->fail('expected a BalanceEmptyException');
        } catch (BalanceEmptyException $e) {
            $sent = $body['error'];
            $this->assertInstanceOf(ProofException::class, $e);
            $this->assertSame(402, $e->statusCode);
            $this->assertSame('balance_empty', $e->errorCode);
            $this->assertSame($sent['top_up_url'], $e->topUpUrl);
            $this->assertSame($sent['used'], $e->used);
            $this->assertSame($sent['included'], $e->included);
            $this->assertSame($sent['balance'], $e->balance);
            $this->assertSame($sent['request_id'], $e->requestId);
        }
        $this->assertSame(0, $mock->count(), 'one request, nothing left queued');
    }

    public function testBillingUnavailableIsAServerExceptionSentOnce(): void
    {
        $refusal = json_encode(['error' => ['code' => 'billing_unavailable', 'message' => 'Billing is not configured']]);
        $fallback = new Response(200, ['Content-Type' => 'application/json'], '{}');
        [$http, $mock] = $this->answering([new Response(503, ['Content-Type' => 'application/json'], $refusal), $fallback], 2);
        try {
            $http->post('/api/v1/billing/checkout', ['plan' => 'pro']);
            $this->fail('expected a BillingUnavailableException');
        } catch (BillingUnavailableException $e) {
            $this->assertInstanceOf(ServerException::class, $e);
            $this->assertSame(503, $e->statusCode);
            $this->assertSame('billing_unavailable', $e->errorCode);
        }
        $this->assertSame(1, $mock->count(), 'the second response was never requested');
    }

    public function testAnyOther503KeepsTheRetries(): void
    {
        $down = fn() => new Response(503, ['Content-Type' => 'application/json'], json_encode(['error' => ['code' => 'service_unavailable', 'message' => 'down']]));
        [$http, $mock] = $this->answering([$down(), $down()], 1);
        try {
            $http->get('/api/v1/billing/balance');
            $this->fail('expected a ServerException');
        } catch (ServerException $e) {
            $this->assertNotInstanceOf(BillingUnavailableException::class, $e);
        }
        $this->assertSame(0, $mock->count(), 'both responses were requested');
    }

    /**
     * 1.2.0's `url` was never sent: `fromArray()` fills the released required string from the key the API
     * does send, where 1.2.0 threw a TypeError on every real response.
     */
    public function testCheckoutAndPortalResponsesFillTheReleasedUrlFromTheKeyTheApiSends(): void
    {
        $checkout = CheckoutResponse::fromArray(['checkout_url' => 'https://checkout.stripe.com/c/pay/cs_1']);
        $this->assertSame('https://checkout.stripe.com/c/pay/cs_1', $checkout->url);
        $this->assertSame('https://checkout.stripe.com/c/pay/cs_1', $checkout->checkout_url);

        $portal = PortalResponse::fromArray(['portal_url' => 'https://billing.stripe.com/p/session/ps_1']);
        $this->assertSame('https://billing.stripe.com/p/session/ps_1', $portal->url);

        // A 1.x positional construction still lands the URL where it did.
        $this->assertSame('https://x.test', (new CheckoutResponse('https://x.test'))->url);
    }
}

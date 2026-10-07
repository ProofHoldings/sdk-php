<?php

declare(strict_types=1);

namespace ProofHoldings\Tests\Integration;

use ProofHoldings\Streaming;
use ReflectionMethod;

class StreamingIntegrationTest extends IntegrationTestCase
{
    private function parseFrame(string $frame): ?array
    {
        return (new ReflectionMethod(Streaming::class, 'parseFrame'))->invoke(null, $frame);
    }

    public function testParsesJsonDataFrame(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped('parser contract-level; covered in mock mode');
        }
        // PHP's Streaming::parseFrame exposes the full envelope {event,id,data};
        // the JS and Python parsers strip to data only. Assert the full shape here.
        $result = $this->parseFrame("id: 1\nevent: status_changed\ndata: {\"status\":\"pending\"}");
        $this->assertSame('status_changed', $result['event']);
        $this->assertSame('1', $result['id']);
        $this->assertSame(['status' => 'pending'], $result['data']);
    }

    public function testJoinsMultilineDataLines(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }
        $result = $this->parseFrame("data: {\"status\":\ndata: \"verified\"}");
        $this->assertSame(['status' => 'verified'], $result['data']);
    }

    public function testReturnsNullForCommentOnlyFrame(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }
        $this->assertNull($this->parseFrame(": heartbeat"));
    }

    public function testReturnsRawStringWhenDataIsNotJson(): void
    {
        if (self::isLiveMode()) {
            $this->markTestSkipped();
        }
        $result = $this->parseFrame("data: not-json-just-a-string");
        $this->assertSame('not-json-just-a-string', $result['data']);
    }
}

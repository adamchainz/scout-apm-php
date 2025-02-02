<?php

declare(strict_types=1);

namespace Scoutapm\UnitTests\Events\Tag;

use Exception;
use PHPUnit\Framework\TestCase;
use Scoutapm\Events\Request\RequestId;
use Scoutapm\Events\Tag\TagRequest;

/** @covers \Scoutapm\Events\Tag\TagRequest */
final class TagRequestTest extends TestCase
{
    /** @throws Exception */
    public function testCanBeInitialized() : void
    {
        $tag = new TagRequest('t', 'v', RequestId::new());
        self::assertNotNull($tag);
    }

    /** @throws Exception */
    public function testJsonSerializes() : void
    {
        $requestId  = RequestId::new();
        $serialized = (new TagRequest('t', 'v', $requestId))->jsonSerialize();

        self::assertIsArray($serialized);
        self::assertArrayHasKey('TagRequest', $serialized[0]);

        $data = $serialized[0]['TagRequest'];
        self::assertSame('t', $data['tag']);
        self::assertSame('v', $data['value']);
        self::assertSame($requestId->toString(), $data['request_id']);
    }
}

<?php

namespace MessageContext\Domain\Tests;

use MessageContext\Domain\ValueObjects\ChannelId;
use ValueObjects\ValueObjectInterface;

class ChannelIdTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldImplementValueObject()
    {
        $ean = new ChannelId("ff556678900");
        $this->assertInstanceOf(ValueObjectInterface::class, $ean);
    }

    public function testFromNative()
    {
        $channel = ChannelId::fromNative("4006381333931");
        $constructedChannel = new ChannelId("4006381333931");

        $this->assertTrue($channel->sameValueAs($constructedChannel));
    }

    public function testSameValueAs()
    {
        $channelA = new ChannelId("4006381333931");
        $channelAEquals = new ChannelId("4006381333931");
        $channelDifferent = new ChannelId("512638133393");

        $this->assertTrue($channelA->sameValueAs($channelAEquals));
        $this->assertFalse($channelA->sameValueAs($channelDifferent));
    }

    public function testToString()
    {
        $channel = new ChannelId("4006381333931");
        $this->assertSame("4006381333931", $channel->__toString());
    }
}

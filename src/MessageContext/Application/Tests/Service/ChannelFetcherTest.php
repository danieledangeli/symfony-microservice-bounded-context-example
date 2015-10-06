<?php

namespace MessageContext\Domain\Tests\Service;

use MessageContext\Application\Exception\ServiceFailureException;
use MessageContext\Application\Exception\ServiceNotAvailableException;
use MessageContext\Application\Service\ChannelFetcher;
use MessageContext\Domain\Tests\MessageContextDomainUnitTest;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelId;

class ChannelFetcherTest extends MessageContextDomainUnitTest
{
    /** @var  ChannelFetcher */
    private $channelFetcher;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $channelGateway;

    public function setUp()
    {
        $this->channelGateway = $this->anyChannelGateway();

        $this->channelFetcher = new ChannelFetcher($this->channelGateway);
    }

    public function testItFetchChannelFromGateway()
    {
        $channelId = new ChannelId("1243");
        $channelFetchedFromGateway = new Channel($channelId, true);

        $this->channelGateway->expects($this->once())
            ->method("getChannel")
            ->with($this->equalTo($channelId))
            ->willReturn($channelFetchedFromGateway);

        $channel = $this->channelFetcher->fetchChannel($channelId);
        $this->assertEquals($channelFetchedFromGateway, $channel);
    }

    /**
     * @dataProvider getFailMicroServicesExceptions
     * @expectedException  \MessageContext\Application\Exception\UnableToPerformActionOnChannel
     */
    public function testItRaiseUnableToCreatePostExceptionOnMicroServiceException($e)
    {
        $channelId = new ChannelId("1243");

        $this->channelGateway->expects($this->once())
            ->method("getChannel")
            ->willThrowException($e);

        $this->channelFetcher->fetchChannel($channelId);
    }

    public function getFailMicroServicesExceptions()
    {
        return [
            [new ServiceFailureException()],
            [new ServiceNotAvailableException()]
        ];
    }
}

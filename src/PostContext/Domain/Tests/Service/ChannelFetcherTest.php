<?php

namespace PostContext\Domain\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Channel;
use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Service\ChannelFetcher;
use PostContext\Domain\Tests\PostContextDomainUnitTest;
use PostContext\Domain\ValueObjects\ChannelId;

class ChannelFetcherTest extends PostContextDomainUnitTest
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
        $channelId = new ChannelId("b12");
        $fetchedFromGatewayChannel = new Channel($channelId, true);

        $this->channelGateway->expects($this->once())
            ->method("getChannel")
            ->willReturn($fetchedFromGatewayChannel);

        $channel = $this->channelFetcher->fetchChannel($channelId);
        $this->assertEquals($fetchedFromGatewayChannel, $channel);
    }

    /**
     * @dataProvider getFailMicroServicesExceptions
     * @expectedException  \PostContext\Domain\Exception\UnableToPerformActionOnChannel
     */
    public function testItRaiseUnableToCreatePostExceptionOnMicroServiceException($e)
    {
        $channelId = new ChannelId("12");

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

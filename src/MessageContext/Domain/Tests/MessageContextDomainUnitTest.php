<?php

namespace MessageContext\Domain\Tests;

use MessageContext\Application\Domain\Gateway\ChannelAuthorizationGatewayInterface;
use MessageContext\Domain\Service\Gateway\ChannelGatewayInterface;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\Publisher;
use MessageContext\Domain\Repository\MessageRepositoryInterface;
use MessageContext\Domain\Repository\PublisherRepositoryInterface;
use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;

abstract class MessageContextDomainUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyPostRepository()
    {
        return $this->getMockBuilder(MessageRepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyPublisherRepository()
    {
        return $this->getMockBuilder(PublisherRepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyChannelGateway()
    {
        return $this->getMockBuilder(ChannelGatewayInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyAuthorizationGateway()
    {
        return $this->getMockBuilder(ChannelAuthorizationGatewayInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyPublisher()
    {
        return new Publisher(new PublisherId());
    }

    /**
     * @param PublisherId $id
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyPublisherWithId(PublisherId $id)
    {
        $publisherMock = $this->anyPublisher();

        $publisherMock->expects($this->any())
            ->method("getId")
            ->willReturn($id);

        return $publisherMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyChannel()
    {
        return $this->anyOpenChannelWithId(new ChannelId("3333"));
    }

    /**
     * @param ChannelId $id
     * @return Channel
     */
    public function anyChannelWithId(ChannelId $id)
    {
        return new Channel($id, false);
    }

    public function anyOpenChannelWithId(ChannelId $id)
    {
        return new Channel($id, false);
    }

    public function anyClosedChannelWithId(ChannelId $id)
    {
        return new Channel($id, true);
    }


    public function anyAuthorizedChannelAuthorization(PublisherId $publisherId, ChannelId $channelId)
    {
        return new ChannelAuthorization($publisherId, $channelId, true);
    }
}

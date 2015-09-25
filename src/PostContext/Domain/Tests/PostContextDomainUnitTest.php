<?php

namespace PostContext\Domain\Tests;

use PostContext\Domain\Channel;
use PostContext\Domain\Gateway\ChannelAuthorizationGatewayInterface;
use PostContext\Domain\Gateway\ChannelGatewayInterface;
use PostContext\Domain\Message;
use PostContext\Domain\Publisher;
use PostContext\Domain\Repository\ChannelRepositoryInterface;
use PostContext\Domain\Repository\PostRepositoryInterface;
use PostContext\Domain\Repository\PublisherRepositoryInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;

abstract class PostContextDomainUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyChannelRepository()
    {
        return $this->getMockBuilder(ChannelRepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyPostRepository()
    {
        return $this->getMockBuilder(PostRepositoryInterface::class)
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
        return $this->getMockBuilder(Publisher::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        return $this->getMockBuilder(Channel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param ChannelId $id
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function anyChannelWithId(ChannelId $id)
    {
        $channelMock = $this->anyChannel();

        $channelMock->expects($this->any())
            ->method("getId")
            ->willReturn($id);

        return $channelMock;
    }

    public function anyPost()
    {
        return $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}

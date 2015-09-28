<?php

namespace PostContext\Domain\Tests;

use PostContext\Domain\Channel;
use PostContext\Domain\Message;
use PostContext\Domain\Publisher;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;
use ValueObjects\Identity\UUID;

class PublisherTest extends PostContextDomainUnitTest
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $channel;
    private $message;

    /** @var  Publisher */
    private $publisher;

    private $publisherMessages;

    public function setUp()
    {
        $this->channel = $this->anyChannelWithId(new ChannelId("3333"));
        $this->message = new BodyMessage("this is a new message in channel");

        $this->publisher = new Publisher(new PublisherId("1264328"));
        $this->publisherMessages = [];
    }

    public function testItPublishMessageInNotClosedChannel()
    {
        $this->channel->expects($this->once())
            ->method("isClosed")
            ->willReturn(false);

        $post = $this->publisher->publishOnChannel($this->channel, $this->message);

        $this->assertInstanceOf(Message::class, $post);
        $this->assertEquals($this->message, $post->getMessage());
    }

    /**
     * @expectedException \PostContext\Domain\Exception\UnableToPerformActionOnChannel
     */
    public function testItNotAllowedToPublishOnClosedChannel()
    {
        $this->channel->expects($this->once())
            ->method("isClosed")
            ->willReturn(true);

        $this->publisher->publishOnChannel($this->channel, $this->message);
    }

    public function testItDeleteTheRightMessageOnClosedChannel()
    {
        $anotherChannel = $this->anyChannelWithId(new ChannelId("1111"));

        $anotherChannel->expects($this->exactly(1))
            ->method("isClosed")
            ->willReturn(false);

        $this->channel->expects($this->exactly(2))
            ->method("isClosed")
            ->willReturn(false);

        $firstMessage = $this->publisher->publishOnChannel($anotherChannel, $this->message);
        $message = $this->publisher->publishOnChannel($this->channel, $this->message);
        $this->publisher->deleteMessage($this->channel, $message->getId());

        $this->assertCount(1, $this->publisher->getMessages());
        $this->assertEquals($firstMessage->getId(), $this->publisher->getMessages()->get(0)->getId());
    }

    /**
     * @expectedException \PostContext\Domain\Exception\PublisherMessageNotFoundException
     */
    public function testItNotDeleteMessagesNotOwnedByHim()
    {
        $anotherChannel = $this->anyChannelWithId(new ChannelId("1111"));

        $anotherChannel->expects($this->exactly(1))
            ->method("isClosed")
            ->willReturn(false);

        $this->channel->expects($this->exactly(1))
            ->method("isClosed")
            ->willReturn(false);

        $firstMessage = $this->publisher->publishOnChannel($anotherChannel, $this->message);

        $this->publisher->deleteMessage($this->channel, new MessageId(UUID::generateAsString()));

        $this->assertCount(1, $this->publisher->getMessages());
        $this->assertEquals($firstMessage->getId(), $this->publisher->getMessages()->get(0)->getId());
    }

    /**
     * @expectedException \PostContext\Domain\Exception\UnableToPerformActionOnChannel
     */
    public function testItNotDeleteMessagesOnClosedChannel()
    {
        $this->channel->expects($this->at(1))
            ->method("isClosed")
            ->willReturn(false);

        //the channel will be closed
        $this->channel->expects($this->at(2))
            ->method("isClosed")
            ->willReturn(true);

        $publishedMessage = $this->publisher->publishOnChannel($this->channel, $this->message);

        $this->shouldNotBeDeleted($publishedMessage);
        $this->publisher->deleteMessage($this->channel, $publishedMessage->getId());
    }

    private function shouldNotBeDeleted($message)
    {
        $this->publisherMessages[] = $message;
    }

    public function tearDown()
    {
        if(count($this->publisherMessages) > 0) {
            $this->assertEquals($this->publisherMessages, $this->publisher->getMessages()->toArray());
        }
    }
}

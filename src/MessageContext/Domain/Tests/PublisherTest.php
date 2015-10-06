<?php

namespace MessageContext\Domain\Tests;

use MessageContext\Domain\Message;
use MessageContext\Domain\Publisher;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;
use ValueObjects\Identity\UUID;

class PublisherTest extends MessageContextDomainUnitTest
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

    /**
     * @group unit
     */
    public function testItPublishMessageInNotClosedChannel()
    {
        $channelId = new ChannelId("3333");
        $channel = $this->anyOpenChannelWithId($channelId);
        $channelAuthorization = $this->anyAuthorizedChannelAuthorization($this->publisher->getId(), $channelId);

        $message = $this->publisher->publishOnChannel($channel, $channelAuthorization, $this->message);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($this->message, $message->getMessage());
    }

    /**
     * @group unit
     * @expectedException \MessageContext\Domain\Exception\ChannelClosedException
     */
    public function testHeIsNotAbleToPublishOnClosedChannel()
    {
        $channelId = new ChannelId("3333");

        $channel = $this->anyClosedChannelWithId($channelId);
        $authorization = $this->anyAuthorizedChannelAuthorization($this->publisher->getId(), $channelId);

        $this->publisher->publishOnChannel($channel, $authorization, $this->message);
    }

    /**
     * @group unit
     * @expectedException \MessageContext\Domain\Exception\MessageNotOwnedByThePublisherException
     */
    public function testHeIsNotAbleToDeleteMessagesNotOwnedByHim()
    {
        $channelId = new ChannelId("1111");
        $channel = $this->anyChannelWithId($channelId);
        $channelAuthorization = $this->anyAuthorizedChannelAuthorization($this->publisher->getId(), $channelId);

        $firstMessage = $this->publisher->publishOnChannel($channel, $channelAuthorization, $this->message);

        $this->publisher->deleteMessage($channel, new MessageId(UUID::generateAsString()));

        $this->assertCount(1, $this->publisher->getMessages());
        $this->assertEquals($firstMessage->getId(), $this->publisher->getMessages()->get(0)->getId());
    }

    /**
     * @group unit
     * @expectedException \MessageContext\Domain\Exception\ChannelClosedException
     */
    public function testItNotDeleteMessagesOnClosedChannel()
    {
        $channelId = new ChannelId("1111");
        $channel = $this->anyClosedChannelWithId($channelId);
        $channelAuthorization = $this->anyAuthorizedChannelAuthorization($this->publisher->getId(), $channelId);

        $publishedMessage = $this->publisher->publishOnChannel($channel, $channelAuthorization, $this->message);

        $this->shouldNotBeDeleted($publishedMessage);
        $this->publisher->deleteMessage($this->channel, $publishedMessage->getId());
    }

    private function shouldNotBeDeleted($message)
    {
        $this->publisherMessages[] = $message;
    }

    public function tearDown()
    {
        if (count($this->publisherMessages) > 0) {
            $this->assertEquals($this->publisherMessages, $this->publisher->getMessages()->toArray());
        }
    }
}

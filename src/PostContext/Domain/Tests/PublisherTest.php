<?php

namespace PostContext\Domain\Tests;

use PostContext\Domain\Message;
use PostContext\Domain\Publisher;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\PublisherId;

class PublisherTest extends PostContextDomainUnitTest
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $channel;
    private $message;

    /** @var  Publisher */
    private $publisher;

    public function setUp()
    {
        $this->channel = $this->anyChannel();
        $this->message = new BodyMessage("this is a new message in channel");

        $this->publisher = new Publisher(new PublisherId("1264328"));
    }

    public function testItPublishMessageInChannel()
    {
        $this->channel->expects($this->once())
            ->method("addPost")
            ->with($this->isInstanceOf(Message::class));


        $this->channel->expects($this->once())
            ->method("getId")
            ->willReturn(new ChannelId("3333"));

        $post = $this->publisher->publishOnChannel($this->channel, $this->message);

        $this->assertInstanceOf(Message::class, $post);
        $this->assertEquals($this->message, $post->getMessage());
    }
}

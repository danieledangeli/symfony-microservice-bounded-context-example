<?php

namespace PostContext\Domain\Tests;

use PostContext\Domain\Message;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;

class PostTest extends PostContextDomainUnitTest
{
    private $channel;
    private $publisher;
    private $message;

    private $post;

    public function setUp()
    {
        $this->channel = $this->anyChannel();
        $this->publisher = $this->anyPublisher();

        $this->message = "this is a new message in channel";
        $this->post = new Message(new PublisherId("4444"), new ChannelId("222"), new BodyMessage($this->message));
    }

    public function testItCreatedPostWithUID()
    {
        $this->assertInstanceOf(MessageId::class, $this->post->getId());
    }
}

<?php

namespace MessageContext\Domain\Tests;

use MessageContext\Domain\Message;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;

class MessageTest extends MessageContextDomainUnitTest
{
    private $message;
    private $messageBody;

    public function setUp()
    {
        $this->messageBody = new BodyMessage("this is a new message in channel");
        $this->message = new Message(new PublisherId("4444"), new ChannelId("222"), $this->messageBody);
    }

    public function testItCreatedPostWithUID()
    {
        $this->assertInstanceOf(MessageId::class, $this->message->getId());
        $this->assertEquals($this->messageBody, $this->message->getMessage());
    }
}

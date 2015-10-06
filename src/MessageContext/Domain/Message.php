<?php

namespace MessageContext\Domain;

use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;
use ValueObjects\DateTime\DateTime;

final class Message
{
    private $messageId;
    private $publisherId;
    private $message;
    private $channelId;
    private $deleted;

    public function __construct(PublisherId $publisherId, ChannelId $channelId, BodyMessage $message)
    {
        $this->messageId = new MessageId();
        $this->publisherId = $publisherId;
        $this->message = $message;
        $this->channelId = $channelId;
        $this->deleted = false;
        $this->createdAt = DateTime::now();
    }

    public function isDeleted()
    {
        return $this->deleted;
    }

    public function getId()
    {
        return $this->messageId;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function getPublisherId()
    {
        return $this->publisherId;
    }
}

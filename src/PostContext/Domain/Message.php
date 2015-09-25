<?php

namespace PostContext\Domain;

use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;

class Message
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

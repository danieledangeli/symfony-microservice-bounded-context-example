<?php

namespace MessageContext\Application\Command;

use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;

final class NewMessageInChannelCommand
{
    public $publisherId;
    public $message;
    public $channelId;

    public function __construct(PublisherId $publisherId, ChannelId $channelId, BodyMessage $message)
    {
        $this->publisherId = $publisherId;
        $this->message = $message;
        $this->channelId = $channelId;
    }
}

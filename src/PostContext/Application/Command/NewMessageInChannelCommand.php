<?php

namespace PostContext\Application\Command;

use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\PublisherId;

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

<?php

namespace MessageContext\Application\Command;

use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;

final class DeleteMessageCommand
{
    public $publisherId;
    public $messageId;

    public function __construct(PublisherId $publisherId, MessageId $messageId)
    {
        $this->publisherId = $publisherId;
        $this->messageId = $messageId;
    }
}

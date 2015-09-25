<?php

namespace PostContext\Application\Command;

use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;

final class DeleteMessageCommand
{
    public $publisherId;
    public $postId;

    public function __construct(PublisherId $publisherId, MessageId $postId)
    {
        $this->publisherId = $publisherId;
        $this->postId = $postId;
    }
}

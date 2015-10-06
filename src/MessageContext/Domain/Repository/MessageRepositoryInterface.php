<?php

namespace MessageContext\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use MessageContext\Domain\Message;
use MessageContext\Domain\ValueObjects\MessageId;

interface MessageRepositoryInterface
{
    /**
     * @param Message $message
     * @return Message
     */
    public function add(Message $message);

    /**
     * @return ArrayCollection <Message>
     */
    public function getAll();

    /**
     * @param MessageId $messageId
     * @return ArrayCollection <Post>
     */
    public function get(MessageId $messageId);

    /**
     * @param MessageId $messageId
     * @return Message
     */
    public function remove(MessageId $messageId);
}

<?php

namespace MessageContext\InfrastructureBundle\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use MessageContext\Domain\Message;
use MessageContext\Domain\Repository\MessageRepositoryInterface;
use MessageContext\Domain\ValueObjects\MessageId;

class MessageRepository implements MessageRepositoryInterface
{
    /** @var ArrayCollection  */
    private $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }
    /**
     * @param Message $message
     * @return Message
     */
    public function add(Message $message)
    {
        $this->messages->add($message);
        return $message;
    }

    /**
     * @return ArrayCollection <Message>
     */
    public function getAll()
    {
        return $this->messages;
    }

    /**
     * @param MessageId $messageId
     * @return ArrayCollection <Message>
     */
    public function get(MessageId $messageId)
    {
        return $this->messages->filter(function (Message $message) use ($messageId) {
            return $message->getId()->sameValueAs($messageId);
        });
    }

    /**
     * @param MessageId $messageId
     * @return Message
     */
    public function remove(MessageId $messageId)
    {
        foreach ($this->messages as $message) {
            if ($message->getId()->sameValueAs($messageId)) {
                $this->messages->removeElement($message);

                return $message;
            }
        }

        //to fix
        return null;
    }
}

<?php

namespace MessageContext\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use MessageContext\Domain\Exception\ChannelClosedException;
use MessageContext\Domain\Exception\MessageNotOwnedByThePublisherException;
use MessageContext\Domain\Exception\PublisherNotAuthorizedException;
use MessageContext\Domain\ValueObjects\BodyMessage;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\MessageId;
use MessageContext\Domain\ValueObjects\PublisherId;

final class Publisher
{
    private $pid;

    /** @var array ArrayCollection */
    private $messages;

    public function __construct(PublisherId $publisherId)
    {
        $this->pid = $publisherId;
        $this->messages = new ArrayCollection();
    }

    /**
     * @param Channel $channel
     * @param ChannelAuthorization $channelAuthorization
     * @param BodyMessage $message
     *
     * @return Message
     * @throws ChannelClosedException
     * @throws PublisherNotAuthorizedException
     */
    public function publishOnChannel(
        Channel $channel,
        ChannelAuthorization $channelAuthorization,
        BodyMessage $message
    ) {
        if ($channelAuthorization->canPublisherPublishOnChannel()) {
            if (!$channel->isClosed()) {
                $message = new Message($this->getId(), $channel->getId(), $message);
                $this->messages->add($message);

                return $message;
            }

            throw new ChannelClosedException(
                sprintf("The channel %s is closed", $channel->getId())
            );
        }

        throw new PublisherNotAuthorizedException(
            sprintf("The publisher %s is not authorized to publish on channel %s",
                $this->getId(), $channel->getId())
        );
    }

    /**
     * @param Channel $channel
     * @param MessageId $messageId
     *
     * @throws ChannelClosedException
     * @throws MessageNotOwnedByThePublisherException
     */
    public function deleteMessage(Channel $channel, MessageId $messageId)
    {
        if (!$channel->isClosed()) {
            $removed = false;

            foreach ($this->messages as $message) {
                if ($message->getId()->sameValueAs($messageId)) {
                    $removed = $this->messages->removeElement($message);
                }
            }

            if (!$removed) {
                throw new MessageNotOwnedByThePublisherException(
                    sprintf("The message id: %s is not owned by the publisher id %s",
                        $messageId,
                        $this->getId()
                    )
                );
            }

            return;
        }

        throw new ChannelClosedException(
            sprintf("The channel %s is closed", $channel->getId())
        );
    }

    public function getId()
    {
        return $this->pid;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}

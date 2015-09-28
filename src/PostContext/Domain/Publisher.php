<?php

namespace PostContext\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Exception\PublisherMessageNotFoundException;
use PostContext\Domain\Exception\UnableToPerformActionOnChannel;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;

class Publisher
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
     * @param BodyMessage $message
     *
     * @return Message
     * @throws UnableToPerformActionOnChannel
     */
    public function publishOnChannel(Channel $channel, BodyMessage $message)
    {
        if (!$channel->isClosed()) {
            $post = new Message($this->getId(), $channel->getId(), $message);
            $this->messages->add($post);

            return $post;
        }

        throw new UnableToPerformActionOnChannel(
            sprintf("The channel %s is closed", $channel->getId())
        );
    }

    /**
     * @param Channel $channel
     * @param MessageId $postId
     *
     * @throws PublisherMessageNotFoundException
     * @throws UnableToPerformActionOnChannel
     */
    public function deleteMessage(Channel $channel, MessageId $postId)
    {
        if (!$channel->isClosed()) {
            $removed = false;

            foreach ($this->messages as $post) {
                if ($post->getId()->sameValueAs($postId)) {
                    $removed = $this->messages->removeElement($post);
                }
            }

            if (!$removed) {
                throw new PublisherMessageNotFoundException(
                    sprintf("The message id: %s is not owned by the publisher id %s",
                        $postId,
                        $this->getId()
                    )
                );
            }

            return;
        }

        throw new UnableToPerformActionOnChannel(
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

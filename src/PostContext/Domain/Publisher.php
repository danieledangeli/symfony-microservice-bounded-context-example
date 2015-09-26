<?php

namespace PostContext\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Exception\PublisherMessageNotFoundException;
use PostContext\Domain\ValueObjects\BodyMessage;
use PostContext\Domain\ValueObjects\MessageId;
use PostContext\Domain\ValueObjects\PublisherId;

class Publisher
{
    private $pid;

    /** @var array ArrayCollection */
    private $posts;

    public function __construct(PublisherId $publisherId)
    {
        $this->pid = $publisherId;
        $this->posts = new ArrayCollection();
    }

    /**
     * @param Channel $channel
     * @param $message
     * @return Message
     */
    public function publishOnChannel(Channel $channel, BodyMessage $message)
    {
        $post = new Message($this->getId(), $channel->getId(), $message);
        $this->posts->add($post);

        return $post;
    }

    /**
     * @param MessageId $postId
     *
     * @throws PublisherMessageNotFoundException
     */
    public function deleteMessage(MessageId $postId)
    {
        $removed = false;

        foreach ($this->posts as $post) {
            if ($post->getId()->sameValueAs($postId)) {
                $removed = true;
                $this->posts->removeElement($post);
            }
        }

        if (!$removed) {
            throw new PublisherMessageNotFoundException(
                sprintf("The post id: %s is not owned by the publisher id %s",
                    $postId,
                    $this->getId()
                )
            );
        }
    }

    public function getId()
    {
        return $this->pid;
    }
}

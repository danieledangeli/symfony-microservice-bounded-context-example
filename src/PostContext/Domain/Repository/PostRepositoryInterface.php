<?php

namespace PostContext\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Message;
use PostContext\Domain\ValueObjects\MessageId;

interface PostRepositoryInterface
{
    /**
     * @param Message $post
     * @return Message
     */
    public function add(Message $post);

    /**
     * @return ArrayCollection <Post>
     */
    public function getAll();

    /**
     * @param MessageId $postId
     * @return ArrayCollection <Post>
     */
    public function get(MessageId $postId);

    /**
     * @param MessageId $postId
     * @return Message
     */
    public function remove(MessageId $postId);
}

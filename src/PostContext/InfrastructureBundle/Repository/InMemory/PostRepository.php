<?php

namespace PostContext\InfrastructureBundle\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Message;
use PostContext\Domain\Repository\PostRepositoryInterface;
use PostContext\Domain\ValueObjects\MessageId;

class PostRepository implements PostRepositoryInterface
{
    /** @var ArrayCollection  */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
    /**
     * @param Message $post
     * @return Message
     */
    public function add(Message $post)
    {
        $this->posts->add($post);
        return $post;
    }

    /**
     * @return ArrayCollection <Post>
     */
    public function getAll()
    {
        return $this->posts;
    }

    /**
     * @param MessageId $postId
     * @return ArrayCollection <Post>
     */
    public function get(MessageId $postId)
    {
        return $this->posts->filter(function (Message $post) use ($postId) {
            return $post->getId()->sameValueAs($postId);
        });
    }

    /**
     * @param MessageId $postId
     * @return Message
     */
    public function remove(MessageId $postId)
    {
        foreach ($this->posts as $post) {
            if ($post->getId()->sameValueAs($postId)) {
                $this->posts->removeElement($post);

                return $post;
            }
        }

        //to fix
        return null;
    }
}

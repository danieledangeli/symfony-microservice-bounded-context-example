<?php

namespace PostContext\Domain;

use PostContext\Domain\ValueObjects\ChannelId;

class Channel
{
    private $channelId;

    private $posts;

    public function __construct($channelId)
    {
        $this->channelId = new ChannelId($channelId);
        $this->posts = [];
    }

    public function getId()
    {
        return $this->channelId;
    }

    public function addPost(Message $post)
    {
        $this->posts[] = $post;
    }

    public function getPosts()
    {
        return $this->posts;
    }
}

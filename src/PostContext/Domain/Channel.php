<?php

namespace PostContext\Domain;

use PostContext\Domain\ValueObjects\ChannelId;

class Channel
{
    private $channelId;
    private $closed;

    public function __construct($channelId, $closed)
    {
        $this->channelId = new ChannelId($channelId);
        $this->closed = $closed;
    }

    public function getId()
    {
        return $this->channelId;
    }

    public function isClosed()
    {
        return $this->closed;
    }
}

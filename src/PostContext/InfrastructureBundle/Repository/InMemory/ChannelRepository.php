<?php

namespace PostContext\InfrastructureBundle\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Channel;
use PostContext\Domain\Repository\ChannelRepositoryInterface;
use PostContext\Domain\ValueObjects\ChannelId;

class ChannelRepository implements ChannelRepositoryInterface
{

    private $channels = ["1", "2"];

    /**
     * @param ChannelId $channelId
     * @return ArrayCollection<Channel> $channels
     */
    public function get(ChannelId $channelId)
    {
        $channels = new ArrayCollection();

        if (in_array($channelId->toNative(), $this->channels)) {
            $channels->add(new Channel($channelId));
        }

        return $channels;
    }

    /**
     * @param Channel $channel
     * @return Channel
     */
    public function add(Channel $channel)
    {
        $this->channels[] = $channel->getId()->toNative();

        return $channel;
    }
}

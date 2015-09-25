<?php

namespace PostContext\Domain\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PostContext\Domain\Channel;
use PostContext\Domain\ValueObjects\ChannelId;

interface ChannelRepositoryInterface
{
    /**
     * @param ChannelId $channelId
     * @return ArrayCollection<Channel> $channels
     */
    public function get(ChannelId $channelId);

    /**
     * @param Channel $channel
     * @return void
     */
    public function add(Channel $channel);
}

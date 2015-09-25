<?php

namespace PostContext\Domain\Gateway;

use PostContext\Domain\Channel;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\ValueObjects\ChannelId;

interface ChannelGatewayInterface extends ServiceIntegrationInterface
{
    /**
     * @param ChannelId $channelId
     * @return Channel
     *
     * @throws ServiceNotAvailableException
     */
    public function getChannel(ChannelId $channelId);
}

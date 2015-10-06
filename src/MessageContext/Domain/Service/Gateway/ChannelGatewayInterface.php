<?php

namespace MessageContext\Domain\Service\Gateway;

use MessageContext\Domain\Exception\MicroServiceIntegrationException;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelId;

interface ChannelGatewayInterface extends ServiceIntegrationInterface
{
    /**
     * @param ChannelId $channelId
     * @return Channel
     *
     * @throws MicroServiceIntegrationException
     */
    public function getChannel(ChannelId $channelId);
}

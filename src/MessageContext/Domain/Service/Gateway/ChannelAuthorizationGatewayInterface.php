<?php

namespace MessageContext\Domain\Service\Gateway;

use MessageContext\Domain\Exception\MicroServiceIntegrationException;
use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;

interface ChannelAuthorizationGatewayInterface extends ServiceIntegrationInterface
{
    /**
     * @param PublisherId $publisherId
     * @param ChannelId $channelId
     *
     * @throws MicroServiceIntegrationException
     * @return ChannelAuthorization
     */
    public function getChannelAuthorization(PublisherId $publisherId, ChannelId $channelId);
}

<?php

namespace PostContext\Domain\Gateway;

use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;

interface ChannelAuthorizationGatewayInterface extends ServiceIntegrationInterface
{
    /**
     * @param PublisherId $publisherId
     * @param ChannelId $channelId
     * @return ChannelAuthorization
     */
    public function getChannelAuthorization(PublisherId $publisherId, ChannelId $channelId);
}

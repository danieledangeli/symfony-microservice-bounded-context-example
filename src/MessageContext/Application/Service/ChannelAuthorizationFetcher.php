<?php

namespace MessageContext\Application\Service;

use MessageContext\Domain\Exception\MicroServiceIntegrationException;
use MessageContext\Application\Exception\UnableToPerformActionOnChannel;
use MessageContext\Domain\Service\Gateway\ChannelAuthorizationGatewayInterface;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;

class ChannelAuthorizationFetcher
{
    private $channelAuthorizationGateway;

    public function __construct(ChannelAuthorizationGatewayInterface $channelAuthorizationGateway)
    {
        $this->channelAuthorizationGateway = $channelAuthorizationGateway;
    }

    public function fetchChannelAuthorization(PublisherId $publisherId, ChannelId $channelId)
    {
        try {
            return $this->channelAuthorizationGateway->getChannelAuthorization($publisherId, $channelId);
        } catch (MicroServiceIntegrationException $e) {
            //the service channel is not available
            //A. the channel doesn't exists
            //B. the channel exist, but we it's impossible to fetch
            //In any case handling a temporary exception

            throw new UnableToPerformActionOnChannel(
                sprintf("Impossible to perform any action on the channel at the moment %s caused by %e",
                    $channelId->toNative(), $e->getMessage())
            );
        }
    }
}

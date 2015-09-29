<?php

namespace PostContext\Domain\Service;

use PostContext\Domain\Exception\ChannelNotFoundException;
use PostContext\Domain\Exception\MicroServiceIntegrationException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Exception\UnableToPerformActionOnChannel;
use PostContext\Domain\Gateway\ChannelGatewayInterface;
use PostContext\Domain\Repository\ChannelRepositoryInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Infrastracture\Gateway\ChannelGateway;

class ChannelFetcher
{
    public function __construct(ChannelGatewayInterface $channelGateway)
    {
        $this->channelGateway = $channelGateway;
    }

    public function fetchChannel(ChannelId $channelId)
    {
        return $this->fetchChannelFromGateway(($channelId));
    }

    private function fetchChannelFromGateway(ChannelId $channelId)
    {
        try {
            $channel = $this->channelGateway->getChannel($channelId);
            return $channel;
        } catch (MicroServiceIntegrationException $e) {
            //the service channel is not available
            //A. the channel doesn't exists
            //B. the channel exist, but we it's impossible to fetch
            //In any case handling a temporary exception

            throw new UnableToPerformActionOnChannel(
                sprintf("Impossible to perform any action in the channel at the moment %s caused by %e",
                    $channelId->toNative(), $e->getMessage())
            );
        }
    }
}

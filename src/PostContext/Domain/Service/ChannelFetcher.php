<?php

namespace PostContext\Domain\Service;

use PostContext\Domain\Exception\ChannelNotFoundException;
use PostContext\Domain\Exception\MicroServiceIntegrationException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Exception\UnableToCreatePostException;
use PostContext\Domain\Gateway\ChannelGatewayInterface;
use PostContext\Domain\Repository\ChannelRepositoryInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Infrastracture\Gateway\ChannelGateway;

class ChannelFetcher
{
    private $channelRepository;
    private $channelGateway;

    public function __construct(ChannelRepositoryInterface $channelRepository, ChannelGatewayInterface $channelGateway)
    {
        $this->channelRepository = $channelRepository;
        $this->channelGateway = $channelGateway;
    }

    public function fetchChannel(ChannelId $channelId)
    {
        $channels = $this->channelRepository->get($channelId);

        if ($channels->count() === 0) {
            $channels->add($this->fetchAndStoreChannelFromGateway($channelId));
        }

        return $channels->get(0);
    }

    private function fetchAndStoreChannelFromGateway(ChannelId $channelId)
    {
        try {
            $channel = $this->channelGateway->getChannel($channelId);
            $this->channelRepository->add($channel);
        } catch (MicroServiceIntegrationException $e) {
            //the service channel is not available
            //A. the channel doesn't exists
            //B. the channel exist, but we it's impossible to fetch
            //In any case handling a temporary exception

            throw new UnableToCreatePostException(sprintf("Impossible to create post in the channel at the moment %s", $channelId->toNative()));
        }

        return $channel;
    }
}

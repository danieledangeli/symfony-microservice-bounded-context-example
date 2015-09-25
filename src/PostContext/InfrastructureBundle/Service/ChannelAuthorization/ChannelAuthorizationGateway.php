<?php

namespace PostContext\InfrastructureBundle\Service\ChannelAuthorization;

use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Gateway\ChannelAuthorizationGatewayInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;

class ChannelAuthorizationGateway implements ChannelAuthorizationGatewayInterface
{
    private $channelAuthorizationAdapter;

    public function __construct(ChannelAuthorizationAdapter $channelAuthorizationAdapter)
    {
        $this->channelAuthorizationAdapter = $channelAuthorizationAdapter;
    }
    /**
     * @param PublisherId $publisherId
     * @param ChannelId $channelId
     *
     * @return \PostContext\Domain\ValueObjects\ChannelAuthorization
     */
    public function getChannelAuthorization(PublisherId $publisherId, ChannelId $channelId)
    {
        return $this->channelAuthorizationAdapter->toChannelAuthorization($publisherId, $channelId);
    }

    /**
     * @param $message
     * @throws ServiceNotAvailableException
     */
    public function onServiceNotAvailable($message)
    {
        throw new ServiceNotAvailableException($message);
    }

    /**
     * @param $message
     * @throws ServiceFailureException
     */
    public function onServiceFailure($message)
    {
        throw new ServiceFailureException($message);
    }
}

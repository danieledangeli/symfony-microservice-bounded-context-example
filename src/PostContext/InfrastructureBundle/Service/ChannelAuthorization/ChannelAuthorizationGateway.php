<?php

namespace PostContext\InfrastructureBundle\Service\ChannelAuthorization;

use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Gateway\ChannelAuthorizationGatewayInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;

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
        try {
            return $this->channelAuthorizationAdapter->toChannelAuthorization($publisherId, $channelId);
        } catch (UnableToProcessResponseFromService $e) {
            $response = $e->getResponse();

            if($response->hasConnectionFailed()) {
                $this->onServiceNotAvailable(sprintf("service channel not available"));
            } else {
                $this->onServiceFailure(
                    sprintf("The service channel auth failed with status code: %s and body %s",
                        $response->getStatusCode(),
                        $response->getBody()
                    )
                );
            }
        }

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

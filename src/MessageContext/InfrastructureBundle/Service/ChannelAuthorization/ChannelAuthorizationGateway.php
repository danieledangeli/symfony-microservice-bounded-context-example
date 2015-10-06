<?php

namespace MessageContext\InfrastructureBundle\Service\ChannelAuthorization;

use MessageContext\Application\Exception\ServiceFailureException;
use MessageContext\Application\Exception\ServiceNotAvailableException;
use MessageContext\Domain\Service\Gateway\ChannelAuthorizationGatewayInterface;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;

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
     * @return \MessageContext\Domain\ValueObjects\ChannelAuthorization
     */
    public function getChannelAuthorization(PublisherId $publisherId, ChannelId $channelId)
    {
        try {
            return $this->channelAuthorizationAdapter->toChannelAuthorization($publisherId, $channelId);
        } catch (UnableToProcessResponseFromService $e) {
            $response = $e->getResponse();

            if ($response->hasConnectionFailed()) {
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

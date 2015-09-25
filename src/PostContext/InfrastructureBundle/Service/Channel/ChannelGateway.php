<?php

namespace PostContext\InfrastructureBundle\Service\Channel;

use PostContext\Domain\Channel;
use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Gateway\ChannelGatewayInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;

class ChannelGateway implements ChannelGatewayInterface
{
    private $channelAdapter;

    public function __construct(ChannelAdapter $channelAdapter)
    {
        $this->channelAdapter = $channelAdapter;
    }

    /**
     * @param ChannelId $channelId
     * @return Channel
     *
     * @throws ServiceNotAvailableException
     */
    public function getChannel(ChannelId $channelId)
    {
        try {
            $channel = $this->channelAdapter->toChannel($channelId);

            return  $channel;
        } catch (UnableToProcessResponseFromService $e) {
            $this->handleNotExpectedResponse($e->getResponse());
        }
    }

    private function handleNotExpectedResponse(Response $response)
    {
        if ($response->hasConnectionFailed()) {
            $this->onServiceNotAvailable("connection failed on channel service");
        }

        $this->onServiceFailure(sprintf("The service channel has failed with message %s", $response->getBody()));
    }

    /**
     * @param $message
     * @throws ServiceNotAvailableException
     */
    public function onServiceNotAvailable($message)
    {
        //react to the service not available
        //
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

<?php

namespace PostContext\InfrastructureBundle\Service\Channel;

use PostContext\Domain\Channel;
use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\Gateway\ChannelGatewayInterface;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\InfrastructureBundle\CircuitBreaker\PostContextCircuitBreakerInterface;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;

class ChannelGateway implements ChannelGatewayInterface
{
    private $channelAdapter;
    private $circuitBreaker;
    private $serviceUniqueName;

    public function __construct(ChannelAdapter $channelAdapter, PostContextCircuitBreakerInterface $circuitBreaker)
    {
        $this->channelAdapter = $channelAdapter;
        $this->circuitBreaker = $circuitBreaker;
        $this->serviceUniqueName = "channel.service";
    }

    /**
     * @param ChannelId $channelId
     * @return Channel
     *
     * @throws ServiceNotAvailableException
     */
    public function getChannel(ChannelId $channelId)
    {
        if ($this->circuitBreaker->isAvailable($this->serviceUniqueName)) {
            try {
                $channel = $this->channelAdapter->toChannel($channelId);
                $this->circuitBreaker->reportSuccess($this->serviceUniqueName);

                return  $channel;
            } catch (UnableToProcessResponseFromService $e) {
                $this->handleNotExpectedResponse($e->getResponse());
            }
        }

        $this->onServiceNotAvailable("The service is not available at the moment");
    }

    private function handleNotExpectedResponse(Response $response)
    {
        $this->circuitBreaker->reportFailure($this->serviceUniqueName);

        if ($response->hasConnectionFailed()) {
            $this->onServiceNotAvailable("connection failed on channel service");
        }

        $this->onServiceFailure(
            sprintf("The service %s has failed with message %s", $this->serviceUniqueName, $response->getBody())
        );
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

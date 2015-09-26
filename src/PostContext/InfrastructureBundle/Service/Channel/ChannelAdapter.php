<?php

namespace PostContext\InfrastructureBundle\Service\Channel;

use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\InfrastructureBundle\RequestHandler\Request;
use PostContext\InfrastructureBundle\RequestHandler\RequestHandler;

/**
 * Class ChannelAdapter
 * Responsable To fetching a channel
 *
 * @package PostContext\InfrastructureBundle\Service
 */
class ChannelAdapter
{
    private $requestHandler;
    private $channelUri;

    public function __construct(RequestHandler $requestHandler, $channelUri)
    {
        $this->requestHandler = $requestHandler;
        $this->channelUri = $channelUri;
    }

    /**
     * @param ChannelId $channelId
     * @return \PostContext\Domain\Channel
     *
     * @throws \PostContext\Domain\Exception\ChannelNotFoundException
     * @throws \PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService
     */
    public function toChannel(ChannelId $channelId)
    {
        $request = new Request("GET", sprintf("%s/api/channels/%s", $this->channelUri, $channelId));
        $request->addHeader("Accept", "application/json");
        $response = $this->requestHandler->handle($request);

        $channelTranslator = new ChannelTranslator();
        return $channelTranslator->toChannelFromResponse($response);
    }
}

<?php

namespace MessageContext\InfrastructureBundle\Service\Channel;

use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\InfrastructureBundle\RequestHandler\Request;
use MessageContext\InfrastructureBundle\RequestHandler\RequestHandler;

/**
 * Class ChannelAdapter
 * Responsable To fetching a channel
 *
 * @package MessageContext\InfrastructureBundle\Service
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
     * @return \MessageContext\Domain\Channel
     *
     * @throws \MessageContext\Domain\Exception\ChannelNotFoundException
     * @throws \MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService
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

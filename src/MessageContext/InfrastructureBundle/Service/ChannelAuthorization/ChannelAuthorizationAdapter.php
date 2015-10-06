<?php

namespace MessageContext\InfrastructureBundle\Service\ChannelAuthorization;

use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\InfrastructureBundle\RequestHandler\Request;
use MessageContext\InfrastructureBundle\RequestHandler\RequestHandler;

class ChannelAuthorizationAdapter
{
    private $requestHandler;
    private $channelAuthorizationUri;

    public function __construct(RequestHandler $requestHandler, $channelAuthorizationUri)
    {
        $this->requestHandler = $requestHandler;
        $this->channelAuthorizationUri = $channelAuthorizationUri;
    }

    /**
     * @param PublisherId $publisherId
     * @param ChannelId $channelId
     *
     * @return ChannelAuthorization
     */
    public function toChannelAuthorization(PublisherId $publisherId, ChannelId $channelId)
    {
        $channelAdapter = new ChannelAuthorizationTranslator();

        $request = new Request("GET", sprintf("%s/api/authorization/channels/%s/users/%s",
                $this->channelAuthorizationUri,
                $channelId,
                $publisherId)
        );

        $request->addHeader("Accept", "application/json");
        $response = $this->requestHandler->handle($request);

        return $channelAdapter->toChannelAuthorizationFromResponse(
            $response
        );
    }
}

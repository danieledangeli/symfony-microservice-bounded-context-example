<?php

namespace PostContext\InfrastructureBundle\Service\ChannelAuthorization;

use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\InfrastructureBundle\RequestHandler\Request;
use PostContext\InfrastructureBundle\RequestHandler\RequestHandler;

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

        return $channelAdapter->toChannelAuthorizationFromResponseBody(
            $response->getBody()
        );
    }
}

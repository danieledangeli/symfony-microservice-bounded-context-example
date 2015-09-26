<?php

namespace PostContext\InfrastructureBundle\Service\ChannelAuthorization;

use PostContext\Domain\Exception\AuthorizationNotFoundException;
use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;

class ChannelAuthorizationTranslator
{
    /**
     * @param Response $response
     * @return ChannelAuthorization
     *
     * @throws AuthorizationNotFoundException
     * @throws UnableToProcessResponseFromService
     */
    public function toChannelAuthorizationFromResponse(Response $response)
    {
        if(200 === $response->getStatusCode()) {
            $responseBodyArray = $response->getBody();

            return new ChannelAuthorization(
                new PublisherId($responseBodyArray["publisher_id"]),
                new ChannelId($responseBodyArray["channel_id"]),
                $responseBodyArray["authorized"]
            );
        }

        if(404 === $response->getStatusCode()) {
            throw new AuthorizationNotFoundException;
        }

        throw new UnableToProcessResponseFromService($response);
    }
}

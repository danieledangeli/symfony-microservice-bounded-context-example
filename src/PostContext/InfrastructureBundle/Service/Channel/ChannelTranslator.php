<?php

namespace PostContext\InfrastructureBundle\Service\Channel;

use PostContext\Domain\Channel;
use PostContext\Domain\Exception\ChannelNotFoundException;
use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;

class ChannelTranslator
{
    public function toChannelFromResponse(Response $response)
    {
        if (200 !== $response->getStatusCode() || 404 === $response->getStatusCode()) {
            throw new UnableToProcessResponseFromService($response);
        }

        if (200 === $response->getStatusCode()) {
            $contentArray = json_decode($response->getBody(), true);
            return new Channel(new ChannelId($contentArray["id"]));
        }

        if (404 === $response->getStatusCode()) {
            throw new ChannelNotFoundException;
        }
    }
}

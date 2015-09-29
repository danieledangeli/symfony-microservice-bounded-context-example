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
        if (200 === $response->getStatusCode()) {
            $contentArray = $this->validateAndGetResponseBodyArray($response);
            return new Channel(new ChannelId($contentArray["id"]), $contentArray["closed"]);
        }

        if (404 === $response->getStatusCode()) {
            throw new ChannelNotFoundException;
        }

        throw new UnableToProcessResponseFromService($response);
    }

    private function validateAndGetResponseBodyArray(Response $response)
    {
        $contentArray = $response->getBody();

        if (isset($contentArray["id"]) && isset($contentArray["closed"])) {
            return $contentArray;
        }

        throw new UnableToProcessResponseFromService(
            $response,
            "Unable to process response body from channel service"
        );
    }
}

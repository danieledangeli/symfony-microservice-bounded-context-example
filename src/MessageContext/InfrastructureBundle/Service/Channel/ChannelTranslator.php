<?php

namespace MessageContext\InfrastructureBundle\Service\Channel;

use MessageContext\Application\Exception\ChannelNotFoundException;
use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use MessageContext\InfrastructureBundle\RequestHandler\Response;

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

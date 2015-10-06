<?php

namespace MessageContext\InfrastructureBundle\Service\ChannelAuthorization;

use MessageContext\Application\Exception\AuthorizationNotFoundException;
use MessageContext\Domain\ValueObjects\ChannelAuthorization;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\Domain\ValueObjects\PublisherId;
use MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use MessageContext\InfrastructureBundle\RequestHandler\Response;

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
        if (200 === $response->getStatusCode()) {
            $responseBodyArray = $this->validateAndGetResponseBodyArray($response);

            return new ChannelAuthorization(
                new PublisherId($responseBodyArray["publisher_id"]),
                new ChannelId($responseBodyArray["channel_id"]),
                $responseBodyArray["authorized"]
            );
        }

        if (404 === $response->getStatusCode()) {
            throw new AuthorizationNotFoundException;
        }

        throw new UnableToProcessResponseFromService($response);
    }

    private function validateAndGetResponseBodyArray(Response $response)
    {
        $contentArray = $response->getBody();

        if (isset($contentArray["publisher_id"]) &&
            isset($contentArray["channel_id"]) &&
            isset($contentArray["authorized"])) {
            return $contentArray;
        }

        throw new UnableToProcessResponseFromService(
            $response,
            "Unable to process response body from channel channel authorization"
        );
    }
}

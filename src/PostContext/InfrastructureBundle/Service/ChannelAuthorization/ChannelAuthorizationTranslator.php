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

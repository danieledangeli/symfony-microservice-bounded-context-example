<?php

namespace PostContext\InfrastructureBundle\Service\ChannelAuthorization;

use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;

class ChannelAuthorizationTranslator
{
    /**
     * @param $responseBody
     * @return ChannelAuthorization
     */
    public function toChannelAuthorizationFromResponseBody($responseBody)
    {
        $responseBodyArray = json_decode($responseBody, true);

        return new ChannelAuthorization(
            new PublisherId($responseBodyArray["publisher_id"]),
            new ChannelId($responseBodyArray["channel_id"]),
            $responseBodyArray["authorized"]
        );
    }
}

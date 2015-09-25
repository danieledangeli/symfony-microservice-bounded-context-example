<?php

namespace PostContext\InfrastructureBundle\Tests\Service\Channel;

use PostContext\InfrastructureBundle\Service\ChannelAuthorization\ChannelAuthorizationTranslator;

class ChannelAuthorizationTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testItTranslateAuthorizedResponse()
    {
        $response = '{"publisherId" : "33333", "channelId":"1", "authorized": true}';

        $channelAuthorizationTranslator = new ChannelAuthorizationTranslator();

        $channelAuthorization = $channelAuthorizationTranslator->toChannelAuthorizationFromResponseBody(
            $response
        );

        $this->assertTrue($channelAuthorization->canPublisherPublishOnChannel());
    }

    public function testItTranslateNotAuthorizedResponse()
    {
        $response = '{"publisherId" : "33333", "channelId":"1", "authorized": false}';

        $channelAuthorizationTranslator = new ChannelAuthorizationTranslator();

        $channelAuthorization = $channelAuthorizationTranslator->toChannelAuthorizationFromResponseBody(
            $response
        );

        $this->assertFalse($channelAuthorization->canPublisherPublishOnChannel());
    }
}

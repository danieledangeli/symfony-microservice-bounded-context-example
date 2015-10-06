<?php

namespace MessageContext\InfrastructureBundle\Tests\Service\Channel;

use MessageContext\InfrastructureBundle\RequestHandler\Response;
use MessageContext\InfrastructureBundle\Service\Channel\ChannelTranslator;
use MessageContext\InfrastructureBundle\Tests\Resources\MockResponsesLocator;

class ChannelTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ChannelTranslator */
    private $channelTranslator;

    public function setUp()
    {
        $this->channelTranslator = new ChannelTranslator();
    }

    public function testItTranslateResponseToChannel()
    {
        $contents = MockResponsesLocator::getResponseTemplate("channel200response.json");
        $responseMock = sprintf($contents, "3333", "true"); //the channelId

        $response = new Response(200);
        $response->setBody(json_decode($responseMock, true));

        $channel = $this->channelTranslator->toChannelFromResponse($response);
        $this->assertEquals("3333", $channel->getId());
    }

    /**
     * @expectedException \MessageContext\Application\Exception\ChannelNotFoundException
     */
    public function testItRaiseChannelNotFoundException()
    {
        $response = new Response(404);
        $this->channelTranslator->toChannelFromResponse($response);
    }

    /**
     * @expectedException \MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService
     * @dataProvider getNotAcceptableStatusCodes
     * @param $statusCode
     */
    public function testItRaiseUnableToProcessResponseException($statusCode)
    {
        $response = new Response($statusCode);
        $this->channelTranslator->toChannelFromResponse($response);
    }

    /**
     * @expectedException \MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService
     */
    public function testItRaiseUnableToProcessResponseIfContentIsNotExpected()
    {
        $response = new Response(200);
        $contents = ["c_id" => 1];

        $response->setBody($contents);

        $this->channelTranslator->toChannelFromResponse($response);
    }

    public function getNotAcceptableStatusCodes()
    {
        return [
            [201], [400], [500], [419], [418],
        ];
    }
}

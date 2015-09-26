<?php

namespace PostContext\InfrastructureBundle\Tests\Service\Channel;

use PostContext\Domain\ValueObjects\ChannelAuthorization;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\Domain\ValueObjects\PublisherId;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;
use PostContext\InfrastructureBundle\Service\ChannelAuthorization\ChannelAuthorizationAdapter;
use PostContext\InfrastructureBundle\Service\ChannelAuthorization\ChannelAuthorizationGateway;

class ChannelAuthorizationGatewayTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ChannelAuthorizationGateway */
    private $channelAuthorizationGateway;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $channelAuthorizationAdapter;

    public function setUp()
    {
        $this->channelAuthorizationAdapter = $this->getMockBuilder(ChannelAuthorizationAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->channelAuthorizationGateway = new ChannelAuthorizationGateway(
            $this->channelAuthorizationAdapter
        );
    }

    public function testItGetChannelAuthorization()
    {
        $publisherId = new PublisherId("3333");
        $channelId = new ChannelId("1");
        $channelAuthorizationExpected = new ChannelAuthorization($publisherId, $channelId, true);

        $this->channelAuthorizationAdapter->expects($this->once())
            ->method("toChannelAuthorization")
            ->with($this->equalTo($publisherId), $this->equalTo($channelId))
            ->willReturn($channelAuthorizationExpected);

        $channelAuthorization = $this->channelAuthorizationGateway->getChannelAuthorization($publisherId, $channelId);

        $this->assertTrue($channelAuthorization->sameValueAs($channelAuthorizationExpected));
    }

    /**
     * @expectedException \PostContext\Domain\Exception\ServiceFailureException
     */
    public function testItReturnServiceFailureException()
    {
        $publisherId = new PublisherId("3333");
        $channelId = new ChannelId("1");

        $this->channelAuthorizationAdapter->expects($this->once())
            ->method("toChannelAuthorization")
            ->with($this->equalTo($publisherId), $this->equalTo($channelId))
            ->willThrowException(new UnableToProcessResponseFromService(new Response(500)));

        $this->channelAuthorizationGateway->getChannelAuthorization($publisherId, $channelId);
    }

    /**
     * @expectedException \PostContext\Domain\Exception\ServiceNotAvailableException
     */
    public function testItReturnServiceNotAvailable()
    {
        $publisherId = new PublisherId("3333");
        $channelId = new ChannelId("1");

        $this->channelAuthorizationAdapter->expects($this->once())
            ->method("toChannelAuthorization")
            ->with($this->equalTo($publisherId), $this->equalTo($channelId))
            ->willThrowException(new UnableToProcessResponseFromService(Response::buildConnectionFailedResponse()));

        $this->channelAuthorizationGateway->getChannelAuthorization($publisherId, $channelId);
    }
}
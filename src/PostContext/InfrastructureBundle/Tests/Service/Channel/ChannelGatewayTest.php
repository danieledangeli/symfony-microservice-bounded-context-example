<?php

namespace PostContext\InfrastructureBundle\Tests\Service\Channel;

use PostContext\Domain\Channel;
use PostContext\Domain\ValueObjects\ChannelId;
use PostContext\InfrastructureBundle\CircuitBreaker\PostContextCircuitBreakerInterface;
use PostContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use PostContext\InfrastructureBundle\RequestHandler\Response;
use PostContext\InfrastructureBundle\Service\Channel\ChannelAdapter;
use PostContext\InfrastructureBundle\Service\Channel\ChannelGateway;
use PostContext\InfrastructureBundle\Tests\Service\GatewayTrait;

class ChannelGatewayTest extends \PHPUnit_Framework_TestCase
{
    use GatewayTrait;

    /** @var  ChannelGateway */
    private $channelGateway;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $channelAdapter;

    private $circuitBreaker;

    public function setUp()
    {
        $this->channelAdapter = $this->getMockBuilder(ChannelAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->circuitBreaker = $this->getMockBuilder(PostContextCircuitBreakerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->channelGateway = new ChannelGateway(
            $this->channelAdapter,
            $this->circuitBreaker
        );
    }

    public function testItGetChannel()
    {
        $channelId = new ChannelId("1");
        $channelExpected = $this->anyChannel();

        $this->channelAdapter->expects($this->once())
            ->method("toChannel")
            ->with($this->equalTo($channelId))
            ->willReturn($channelExpected);

        $this->theServiceIsAvailable();
        $this->itReportSuccess();

        $channel = $this->channelGateway->getChannel($channelId);

        $this->assertEquals($channelExpected, $channel);
    }

    /**
     * @expectedException \PostContext\Domain\Exception\ServiceFailureException
     */
    public function testItReturnServiceFailureException()
    {
        $channelId = new ChannelId("1");

        $this->channelAdapter->expects($this->once())
            ->method("toChannel")
            ->with($this->equalTo($channelId))
            ->willThrowException(new UnableToProcessResponseFromService(new Response(500)));

        $this->theServiceIsAvailable();
        $this->itReportFailure();

        $this->channelGateway->getChannel($channelId);
    }

    /**
     * @expectedException \PostContext\Domain\Exception\ServiceNotAvailableException
     */
    public function testItReturnServiceNotAvailableException()
    {
        $channelId = new ChannelId("1");

        $this->channelAdapter->expects($this->once())
            ->method("toChannel")
            ->with($this->equalTo($channelId))
            ->willThrowException(new UnableToProcessResponseFromService(Response::buildConnectionFailedResponse()));

        $this->theServiceIsAvailable();
        $this->itReportFailure();

        $this->channelGateway->getChannel($channelId);
    }

    /**
     * @expectedException \PostContext\Domain\Exception\ServiceNotAvailableException
     */
    public function testItReturnServiceNotAvailableExceptionIfServiceIsNotAvailable()
    {
        $channelId = new ChannelId("1");

        $this->theServiceIsNotAvailable();
        $this->channelAdapter->expects($this->never())
            ->method("toChannel");

        $this->channelGateway->getChannel($channelId);
    }

    private function anyChannel()
    {
        return  $this->getMockBuilder(Channel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}

<?php

namespace MessageContext\InfrastructureBundle\Tests\Service\Channel;

use MessageContext\Domain\ValueObjects\Channel;
use MessageContext\Domain\ValueObjects\ChannelId;
use MessageContext\InfrastructureBundle\CircuitBreaker\MessageContextCircuitBreakerInterface;
use MessageContext\InfrastructureBundle\Exception\UnableToProcessResponseFromService;
use MessageContext\InfrastructureBundle\RequestHandler\Response;
use MessageContext\InfrastructureBundle\Service\Channel\ChannelAdapter;
use MessageContext\InfrastructureBundle\Service\Channel\ChannelGateway;
use MessageContext\InfrastructureBundle\Tests\Service\GatewayTrait;

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

        $this->circuitBreaker = $this->getMockBuilder(MessageContextCircuitBreakerInterface::class)
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
     * @expectedException \MessageContext\Application\Exception\ServiceFailureException
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
     * @expectedException \MessageContext\Application\Exception\ServiceNotAvailableException
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
     * @expectedException \MessageContext\Application\Exception\ServiceNotAvailableException
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
        return  new Channel(new ChannelId("3333"), false);
    }
}

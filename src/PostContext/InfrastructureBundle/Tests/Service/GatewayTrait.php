<?php

namespace PostContext\InfrastructureBundle\Tests\Service;

trait GatewayTrait
{
    public function theServiceIsAvailable()
    {
        $this->circuitBreaker->expects($this->once())
            ->method("isAvailable")
            ->willReturn(true);
    }

    public function theServiceIsNotAvailable()
    {
        $this->circuitBreaker->expects($this->once())
            ->method("isAvailable")
            ->willReturn(false);
    }

    public function itReportFailure()
    {
        $this->circuitBreaker->expects($this->once())
            ->method("reportFailure");
    }

    public function itReportSuccess()
    {
        $this->circuitBreaker->expects($this->once())
            ->method("reportSuccess");
    }
}

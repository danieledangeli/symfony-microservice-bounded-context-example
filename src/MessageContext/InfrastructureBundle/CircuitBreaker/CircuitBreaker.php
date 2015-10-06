<?php

namespace MessageContext\InfrastructureBundle\CircuitBreaker;

use Ejsmont\CircuitBreaker\CircuitBreakerInterface;

class CircuitBreaker implements MessageContextCircuitBreakerInterface
{
    private $circuitBreaker;

    public function __construct(CircuitBreakerInterface $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
    }

    public function isAvailable($serviceName)
    {
        return $this->circuitBreaker->isAvailable($serviceName);
    }

    public function reportSuccess($serviceName)
    {
        $this->circuitBreaker->reportSuccess($serviceName);
    }

    public function reportFailure($serviceName)
    {
        $this->circuitBreaker->reportFailure($serviceName);
    }
}

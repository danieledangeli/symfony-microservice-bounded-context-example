<?php

namespace PostContext\InfrastructureBundle\CircuitBreaker;

use Doctrine\Common\Cache\Cache;
use Ejsmont\CircuitBreaker\CircuitBreakerInterface;
use Ejsmont\CircuitBreaker\Factory;
use Ejsmont\CircuitBreaker\Storage\Decorator\ArrayDecorator;

class CircuitBreaker implements PostContextCircuitBreakerInterface
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

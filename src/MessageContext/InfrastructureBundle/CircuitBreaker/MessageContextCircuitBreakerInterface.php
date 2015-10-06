<?php

namespace MessageContext\InfrastructureBundle\CircuitBreaker;

interface MessageContextCircuitBreakerInterface
{
    public function isAvailable($serviceName);
    public function reportSuccess($serviceName);
    public function reportFailure($serviceName);
}

<?php

namespace PostContext\InfrastructureBundle\CircuitBreaker;

interface CircuitBreakerInterface
{
    public function isAvailable($serviceName);
}

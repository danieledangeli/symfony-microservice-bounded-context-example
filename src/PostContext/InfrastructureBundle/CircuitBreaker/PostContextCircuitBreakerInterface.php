<?php

namespace PostContext\InfrastructureBundle\CircuitBreaker;

interface PostContextCircuitBreakerInterface
{
    public function isAvailable($serviceName);
    public function reportSuccess($serviceName);
    public function reportFailure($serviceName);
}
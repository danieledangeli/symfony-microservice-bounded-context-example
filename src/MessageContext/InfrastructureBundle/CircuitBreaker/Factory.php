<?php

namespace MessageContext\InfrastructureBundle\CircuitBreaker;

use Doctrine\Common\Cache\Cache;
use Ejsmont\CircuitBreaker\CircuitBreakerInterface;
use Ejsmont\CircuitBreaker\Core\CircuitBreaker as EjsmontCircuitBreaker;
use Ejsmont\CircuitBreaker\Storage\Decorator\ArrayDecorator;

/**
 * Class allows easy assembly of circuit breaker instances using Doctrine cache objects.
 */
class Factory
{
    /**
     * Creates a circuit breaker instance using doctrine cache adapter.
     *
     * @param Cache     $doctrineCache  instance of a doctrine cache backend to use
     * @param int       $maxFailures    how many times do we allow service to fail before considering it offline
     * @param int       $retryTimeout   how many seconds should we wait before attempting retry
     *
     * @return CircuitBreakerInterface
     */
    public static function getDoctrineCacheInstance(Cache $doctrineCache, $maxFailures = 20, $retryTimeout = 30)
    {
        $storage = new ArrayDecorator(new DoctrineCacheAdapter($doctrineCache));
        return new EjsmontCircuitBreaker($storage, $maxFailures, $retryTimeout);
    }
}

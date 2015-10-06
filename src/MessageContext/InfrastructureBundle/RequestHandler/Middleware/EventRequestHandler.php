<?php

namespace MessageContext\InfrastructureBundle\RequestHandler\Middleware;

use MessageContext\InfrastructureBundle\RequestHandler\Event\ReceivedResponse;
use MessageContext\InfrastructureBundle\RequestHandler\Request;
use MessageContext\InfrastructureBundle\RequestHandler\RequestHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventRequestHandler implements RequestHandler
{
    private $eventDispatcher;
    private $requestHandler;

    public function __construct(EventDispatcherInterface $eventDispatcher, RequestHandler $requestHandler)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestHandler = $requestHandler;
    }

    public function handle(Request $request)
    {
        $response = $this->requestHandler->handle($request);
        $this->eventDispatcher->dispatch('request_handler.received_response', new ReceivedResponse($response));

        return $response;
    }
}

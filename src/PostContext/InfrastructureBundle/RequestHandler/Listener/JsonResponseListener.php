<?php

namespace PostContext\InfrastructureBundle\RequestHandler\Listener;

use Exception;
use PostContext\InfrastructureBundle\RequestHandler\Event\ReceivedResponse;

class JsonResponseListener
{
    public function onReceivedResponse(ReceivedResponse $receivedResponse)
    {
        $response = $receivedResponse->getResponse();
        if (false === strpos($response->getHeader('Content-Type'), 'application/json')) {
            return;
        }

        $body = $response->getBody();
        $json = json_decode($body, true);

        if (json_last_error()) {
            throw new Exception("Invalid JSON in response body: $body");
        }

        $response->setBody($json);
    }
}

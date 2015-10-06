<?php

namespace MessageContext\InfrastructureBundle\Exception;

use MessageContext\InfrastructureBundle\RequestHandler\Response;

class UnableToProcessResponseFromService extends \Exception
{
    private $response;

    public function __construct(Response $response, $message = "")
    {
        parent::__construct($message);
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

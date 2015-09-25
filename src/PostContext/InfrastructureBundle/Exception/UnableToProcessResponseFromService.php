<?php

namespace PostContext\InfrastructureBundle\Exception;

use PostContext\InfrastructureBundle\RequestHandler\Response;

class UnableToProcessResponseFromService extends \Exception
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

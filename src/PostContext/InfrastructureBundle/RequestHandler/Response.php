<?php

namespace PostContext\InfrastructureBundle\RequestHandler;

final class Response
{
    private $statusCode;
    private $headers = array();
    private $body;
    private $connectionFailed;

    public function __construct($statusCode)
    {
        $this->statusCode = $statusCode;
        $this->connectionFailed = false;
    }

    public static function buildConnectionFailedResponse()
    {
        $response = new self(0);
        $response->connectionFailed = true;

        return $response;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeader($name)
    {
        $header = isset($this->headers[$name]) ? $this->headers[$name] : null;
        if (null !== $header && is_array($header)) {
            return $header[0];
        }

        return $header;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function hasConnectionFailed()
    {
        return $this->connectionFailed;
    }
}

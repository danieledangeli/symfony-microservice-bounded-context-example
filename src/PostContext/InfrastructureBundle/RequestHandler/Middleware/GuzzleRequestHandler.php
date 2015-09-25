<?php

namespace PostContext\InfrastructureBundle\RequestHandler\Middleware;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use PostContext\InfrastructureBundle\RequestHandler\Request;
use PostContext\InfrastructureBundle\RequestHandler\RequestHandler;
use PostContext\InfrastructureBundle\RequestHandler\Response;

class GuzzleRequestHandler implements RequestHandler
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle(Request $request)
    {
        $guzzleRequest = $this->client->createRequest($request->getVerb(), "http://www.google.com:81", array(
            'headers' => $request->getHeaders(),
            'body' => $request->getBody(),
        ));

        $response = null;

        try {
            $guzzleResponse = $this->client->send($guzzleRequest);
            $response = new Response($guzzleResponse->getStatusCode());
            $response->setHeaders($guzzleResponse->getHeaders());
        } catch (ConnectException $e) {
            $response = Response::buildConnectionFailedResponse();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $guzzleResponse = $e->getResponse();
                $response = new Response($guzzleResponse->getStatusCode());
                $response->setHeaders($guzzleResponse->getHeaders());
                if (null !== $guzzleResponse->getBody()) {
                    $response->setBody($guzzleResponse->getBody()->__toString());
                }
            }
        }

        return $response;
    }
}

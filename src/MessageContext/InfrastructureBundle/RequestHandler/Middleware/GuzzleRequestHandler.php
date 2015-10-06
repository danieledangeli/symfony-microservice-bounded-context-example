<?php

namespace MessageContext\InfrastructureBundle\RequestHandler\Middleware;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use MessageContext\InfrastructureBundle\RequestHandler\Request;
use MessageContext\InfrastructureBundle\RequestHandler\RequestHandler;
use MessageContext\InfrastructureBundle\RequestHandler\Response;

class GuzzleRequestHandler implements RequestHandler
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $guzzleRequest = $this->client->createRequest($request->getVerb(), $request->getUri(), array(
            'headers' => $request->getHeaders(),
            'body' => $request->getBody(),
        ));

        try {
            $guzzleResponse = $this->client->send($guzzleRequest);
            $response = new Response($guzzleResponse->getStatusCode());
            $response->setHeaders($guzzleResponse->getHeaders());
            $response->setBody($guzzleResponse->getBody()->__toString());

            return $response;
        } catch (ConnectException $e) {
            return $this->handleConnectionException();
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        }
    }

    private function handleConnectionException()
    {
        return Response::buildConnectionFailedResponse();
    }

    private function handleRequestException(RequestException $e)
    {
        if ($e->hasResponse()) {
            $guzzleResponse = $e->getResponse();

            $response = new Response($guzzleResponse->getStatusCode());
            $response->setHeaders($guzzleResponse->getHeaders());

            if (null !== $guzzleResponse->getBody()) {
                $response->setBody($guzzleResponse->getBody()->__toString());
            }

            return $response;
        }

        return Response::buildConnectionFailedResponse();
    }
}

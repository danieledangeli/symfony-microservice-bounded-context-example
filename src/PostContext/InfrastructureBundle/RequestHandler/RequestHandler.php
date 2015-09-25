<?php

namespace PostContext\InfrastructureBundle\RequestHandler;

interface RequestHandler
{
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request);
}

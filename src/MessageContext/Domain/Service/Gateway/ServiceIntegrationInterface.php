<?php

namespace MessageContext\Domain\Service\Gateway;

use MessageContext\Domain\Exception\MicroServiceIntegrationException;

interface ServiceIntegrationInterface
{
    /**
     * @param $message
     *
     * @throws MicroServiceIntegrationException
     */
    public function onServiceNotAvailable($message);

    /**
     * @param $message
     *
     * @throws MicroServiceIntegrationException
     */
    public function onServiceFailure($message);
}

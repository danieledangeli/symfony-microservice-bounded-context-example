<?php

namespace PostContext\Domain\Gateway;

use PostContext\Domain\Exception\ServiceFailureException;
use PostContext\Domain\Exception\ServiceNotAvailableException;

interface ServiceIntegrationInterface
{
    /**
     * @param $message
     *
     * @throw ServiceNotAvailableException
     */
    public function onServiceNotAvailable($message);

    /**
     * @param $message
     *
     * @throw ServiceFailureException
     */
    public function onServiceFailure($message);
}

<?php

namespace MessageContext\InfrastructureBundle;

use MessageContext\InfrastructureBundle\DependencyInjection\InfrastructureBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InfrastructureBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new InfrastructureBundleExtension();
    }
}

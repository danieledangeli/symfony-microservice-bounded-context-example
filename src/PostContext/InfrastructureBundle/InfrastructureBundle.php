<?php

namespace PostContext\InfrastructureBundle;

use PostContext\InfrastructureBundle\DependencyInjection\InfrastructureBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InfrastructureBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new InfrastructureBundleExtension();
    }
}

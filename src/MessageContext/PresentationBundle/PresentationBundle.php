<?php

namespace MessageContext\PresentationBundle;

use MessageContext\PresentationBundle\DependencyInjection\PresentationBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PresentationBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PresentationBundleExtension();
    }
}

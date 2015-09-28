<?php

use Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        //ack for .env vars
        //load the .env vars if required
        if($this->getEnvironment() === "test") {
            if(file_exists(__DIR__ . '/../.env')) {
                $dotenv = new Dotenv(__DIR__ . '/../');
                $dotenv->load();
            }
        }

        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new \PostContext\PresentationBundle\PresentationBundle(),
            new \PostContext\InfrastructureBundle\InfrastructureBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\TwigBundle\TwigBundle();

        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $loader->load(function ($container) {
                $container->loadFromExtension('web_profiler', array(
                    'toolbar' => true,
                ));
                $container->loadFromExtension('framework', array(
                    'test' => true,
                ));
            });
        }
    }
}

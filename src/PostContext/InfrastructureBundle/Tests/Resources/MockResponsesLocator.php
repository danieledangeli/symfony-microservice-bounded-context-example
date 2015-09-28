<?php

namespace PostContext\InfrastructureBundle\Tests\Resources;

class MockResponsesLocator
{
    public static function getResponseTemplate($mockName)
    {
        $rootPath = dirname(__FILE__);
        $filePath = realpath($rootPath . '/'.$mockName);

        if (file_exists($filePath) === false) {
            throw new \RuntimeException("The file $filePath does not exist");
        }
        return file_get_contents($filePath);
    }
}

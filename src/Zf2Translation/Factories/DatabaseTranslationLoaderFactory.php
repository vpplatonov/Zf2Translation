<?php

namespace Zf2Translation\Factories;

use Zf2Translation\Loader\DatabaseTranslationLoader;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatabaseTranslationLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // service created in Loader
        $sm = $serviceLocator->getServiceLocator();
        return new DatabaseTranslationLoader($sm);
    }
}

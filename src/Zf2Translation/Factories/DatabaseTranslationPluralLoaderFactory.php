<?php

namespace Zf2Translation\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatabaseTranslationPluralLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        return new DatabaseTranslationPluralLoader($sm);
    }
}
<?php

namespace Zf2Translation\Translator;

use Zend\I18n\Translator\Translator;
use Zend\Mvc\I18n\DummyTranslator;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Mvc\Service\TranslatorServiceFactory as BaseTranslatorFactory;
use Zend\I18n\Translator\LoaderPluginManager as LoaderPluginManager;

use Zf2Translation\Factories\DatabaseTranslationLoaderFactory;
use Zend\ServiceManager\Config as Config;
//use Zend\Mvc\Service\ConfigFactory as Config;

use Zf2Translation\Delegator\DatabaseLoaderDelegatorFactory;

class TranslatorServiceFactory extends BaseTranslatorFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return MvcTranslator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = parent::createService($serviceLocator);

        $config = $serviceLocator->get('Config');
        $pluginManagerConfig = isset($config['translator']['loaderpluginmanager']) ? $config['translator']['loaderpluginmanager'] : array();
        $pluginManager = new LoaderPluginManager(new Config($pluginManagerConfig));
        $pluginManager->setServiceLocator($serviceLocator);
        $translator->setPluginManager($pluginManager);

        return $translator;
    }
}

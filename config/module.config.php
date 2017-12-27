<?php

return [

    'service_manager' => array(
            'aliases' => array(
                    'zf2translation_dbadapter' => 'Zend\Db\Adapter\Adapter',
            ),
    ),

    'translator' => [
        'loaderpluginmanager' => [
            'factories' => [
                'database' => function($lpm)
                              {
                                $sm = $lpm->getServiceLocator();
                                $loader = new Zf2Translation\Loader\DatabaseTranslationLoader($sm);
                                return $loader;
                              },
                'databaseplural' => function($lpm)
                              {
                                $sm = $lpm->getServiceLocator();
                                $loader = new Zf2Translation\Loader\DatabaseTranslationPluralLoader($sm);
                                return $loader;
                              },
            ],
        ],
        'remote_translation' => [
            [
                'type' => 'database', //This sets the database loader for the default textDomain
            ],
        ],
    ]
];

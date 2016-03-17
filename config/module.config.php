<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2015 ITEA Office (https://itea3.org]
 */
use Program\Acl;
use Program\Controller;
use Program\Factory;
use Program\Options;
use Program\Service;
use Program\View\Helper;

$config = [
    'controllers'     => [
        'invokables'         => [
            //Controller\ProgramManagerController::class     ,
            //Controller\CallManagerController::class        ,
            //Controller\NdaManagerController::class         ,
            //Controller\NdaController::class                ,
            //Controller\DoaController::class                ,
            //Controller\FunderManagerController::class      ,
            //Controller\CallCountryManagerController::class ,
            //Controller\SessionController::class            ,
        ],
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'invokables' => [
            'callSessionLink'     => Helper\CallSessionLink::class,
            'programHandler'      => Helper\ProgramHandler::class,
            'callServiceProxy'    => Helper\CallServiceProxy::class,
            'programServiceProxy' => Helper\ProgramServiceProxy::class,
            'callInformationBox'  => Helper\CallInformationBox::class,
            'programLink'         => Helper\ProgramLink::class,
            'programDoaLink'      => Helper\DoaLink::class,
            'callLink'            => Helper\CallLink::class,
            'ndaLink'             => Helper\NdaLink::class,
            'funderLink'          => Helper\FunderLink::class,
            'callCountryLink'     => Helper\CallCountryLink::class,
        ]
    ],
    'service_manager' => [
        'factories'          => [
            Service\ProgramService::class => Factory\ProgramServiceFactory::class,
            Service\CallService::class    => Factory\CallServiceFactory::class,
            Service\FormService::class    => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class  => Factory\ModuleOptionsFactory::class,
            //Assertion\Nda::class,
            //Assertion\Doa::class,
            //Assertion\Funder::class,
        ],
        'abstract_factories' => [
            Acl\Factory\AssertionInvokableAbstractFactory::class
        ],
        'invokables'         => [
            'program_program_form_filter' => 'Program\Form\FilterCreateObject',
            'program_call_form_filter'    => 'Program\Form\FilterCreateObject',
            'program_nda_form_filter'     => 'Program\Form\FilterCreateObject',
            'program_country_form_filter' => 'Program\Form\FilterCreateObject',
            'program_funder_form_filter'  => 'Program\Form\FilterCreateObject'
        ]
    ],
    'doctrine'        => [
        'driver'       => [
            'program_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Program/Entity/']
            ],
            'orm_default'               => [
                'drivers' => [
                    'Program\Entity' => 'program_annotation_driver',
                ]
            ]
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                ]
            ],
        ],
    ]
];
$configFiles = [
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
    __DIR__ . '/module.option.program.php',
];
foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}
return $config;

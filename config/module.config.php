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
use Program\View;

$config = [
    'controllers'     => [
        'abstract_factories' => [
            Controller\Factory\ControllerInvokableAbstractFactory::class
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'aliases'   => [
            'callSessionLink'     => View\Helper\CallSessionLink::class,
            'programHandler'      => View\Helper\ProgramHandler::class,
            'callServiceProxy'    => View\Helper\CallServiceProxy::class,
            'programServiceProxy' => View\Helper\ProgramServiceProxy::class,
            'callInformationBox'  => View\Helper\CallInformationBox::class,
            'programLink'         => View\Helper\ProgramLink::class,
            'programDoaLink'      => View\Helper\DoaLink::class,
            'callLink'            => View\Helper\CallLink::class,
            'ndaLink'             => View\Helper\NdaLink::class,
            'funderLink'          => View\Helper\FunderLink::class,
            'callCountryLink'     => View\Helper\CallCountryLink::class,
        ],
        'factories' => [
            View\Helper\CallSessionLink::class     => View\Factory\LinkInvokableFactory::class,
            View\Helper\ProgramHandler::class      => View\Factory\LinkInvokableFactory::class,
            View\Helper\CallServiceProxy::class    => View\Factory\LinkInvokableFactory::class,
            View\Helper\ProgramServiceProxy::class => View\Factory\LinkInvokableFactory::class,
            View\Helper\CallInformationBox::class  => View\Factory\LinkInvokableFactory::class,
            View\Helper\ProgramLink::class         => View\Factory\LinkInvokableFactory::class,
            View\Helper\DoaLink::class             => View\Factory\LinkInvokableFactory::class,
            View\Helper\CallLink::class            => View\Factory\LinkInvokableFactory::class,
            View\Helper\NdaLink::class             => View\Factory\LinkInvokableFactory::class,
            View\Helper\FunderLink::class          => View\Factory\LinkInvokableFactory::class,
            View\Helper\CallCountryLink::class     => View\Factory\LinkInvokableFactory::class,
        ]
    ],
    'service_manager' => [
        'factories'          => [
            Service\ProgramService::class => Factory\ProgramServiceFactory::class,
            Service\CallService::class    => Factory\CallServiceFactory::class,
            Service\FormService::class    => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class  => Factory\ModuleOptionsFactory::class,
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
                'paths' => [__DIR__ . '/../src/Entity/']
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

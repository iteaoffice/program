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
    'controllers'        => [
        'factories' => [
            Controller\CallCountryManagerController::class => Controller\Factory\ControllerFactory::class,
            Controller\CallManagerController::class        => Controller\Factory\ControllerFactory::class,
            Controller\DoaController::class                => Controller\Factory\ControllerFactory::class,
            Controller\FunderManagerController::class      => Controller\Factory\ControllerFactory::class,
            Controller\NdaController::class                => Controller\Factory\ControllerFactory::class,
            Controller\NdaManagerController::class         => Controller\Factory\ControllerFactory::class,
            Controller\ProgramManagerController::class     => Controller\Factory\ControllerFactory::class,
            Controller\SessionController::class            => Controller\Factory\ControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getProgramFilter' => Controller\Plugin\GetFilter::class,
            'renderNda'        => Controller\Plugin\RenderNda::class,
            'renderProgramDoa' => Controller\Plugin\RenderDoa::class,
            'renderSession'    => Controller\Plugin\RenderSession::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class     => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderNda::class     => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderDoa::class     => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderSession::class => Controller\Factory\PluginFactory::class,
        ]
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'   => [
            'callSessionLink'    => View\Helper\CallSessionLink::class,
            'programHandler'     => View\Helper\ProgramHandler::class,
            'callInformationBox' => View\Helper\CallInformationBox::class,
            'programLink'        => View\Helper\ProgramLink::class,
            'programDoaLink'     => View\Helper\DoaLink::class,
            'callLink'           => View\Helper\CallLink::class,
            'ndaLink'            => View\Helper\NdaLink::class,
            'funderLink'         => View\Helper\FunderLink::class,
            'callCountryLink'    => View\Helper\CallCountryLink::class,
        ],
        'factories' => [
            View\Helper\CallSessionLink::class    => View\Factory\ViewHelperFactory::class,
            View\Helper\ProgramHandler::class     => View\Factory\ViewHelperFactory::class,
            View\Helper\CallInformationBox::class => View\Factory\ViewHelperFactory::class,
            View\Helper\ProgramLink::class        => View\Factory\ViewHelperFactory::class,
            View\Helper\DoaLink::class            => View\Factory\ViewHelperFactory::class,
            View\Helper\CallLink::class           => View\Factory\ViewHelperFactory::class,
            View\Helper\NdaLink::class            => View\Factory\ViewHelperFactory::class,
            View\Helper\FunderLink::class         => View\Factory\ViewHelperFactory::class,
            View\Helper\CallCountryLink::class    => View\Factory\ViewHelperFactory::class,
        ]
    ],
    'service_manager'    => [
        'factories' => [
            Service\ProgramService::class => Factory\ProgramServiceFactory::class,
            Service\CallService::class    => Factory\CallServiceFactory::class,
            Service\FormService::class    => Factory\FormServiceFactory::class,
            Options\ModuleOptions::class  => Factory\ModuleOptionsFactory::class,
            Acl\Assertion\Doa::class      => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Funder::class   => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Nda::class      => Acl\Factory\AssertionFactory::class,
        ],
    ],
    'doctrine'           => [
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

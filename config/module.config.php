<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Program\Acl\Assertion;
use Program\Controller;
use Program\Controller\ControllerInitializer;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Program\Service\ServiceInitializer;
use Program\View\Helper;

$config = [
    'controllers'     => [
        'initializers' => [
            ControllerInitializer::class
        ],
        'invokables'   => [
            'program'                                 => Controller\ProgramController::class,
            'program-manager'                         => Controller\ProgramManagerController::class,
            'nda-manager'                             => Controller\NdaManagerController::class,
            'program-nda'                             => Controller\NdaController::class,
            'program-doa'                             => Controller\DoaController::class,
            Controller\FunderManagerController::class => Controller\FunderManagerController::class,
            Controller\SessionController::class       => Controller\SessionController::class
        ],
        'factories'    => [
            'program_module_options' => 'Program\Factory\OptionServiceFactory',
        ]
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
        ]
    ],
    'service_manager' => [
        'initializers' => [ServiceInitializer::class],
        'factories'    => [
            'program_module_options'         => 'Program\Service\OptionServiceFactory',
            'program_nda_navigation_service' => 'Program\Navigation\Factory\NdaNavigationServiceFactory',
        ],
        'invokables'   => [
            Assertion\Nda::class          => Assertion\Nda::class,
            Assertion\Doa::class          => Assertion\Doa::class,
            Assertion\Funder::class       => Assertion\Funder::class,
            ProgramService::class         => ProgramService::class,
            CallService::class            => CallService::class,
            FormService::class            => FormService::class,
            'program_program_form_filter' => 'Program\Form\FilterCreateObject',
            'program_call_form_filter'    => 'Program\Form\FilterCreateObject',
            'program_nda_form_filter'     => 'Program\Form\FilterCreateObject',
            'program_funder_form_filter'  => 'Program\Form\FilterCreateObject'
        ]
    ],
    'doctrine'        => [
        'driver'       => [
            'program_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Program/Entity/']
            ],
            'orm_default'               => ['drivers' => ['Program\Entity' => 'program_annotation_driver',]]
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

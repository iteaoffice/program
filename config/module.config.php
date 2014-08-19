<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Acl\Assertion\Nda as NdaAssertion;
use Program\Controller\ControllerInitializer;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Program\Service\ServiceInitializer;

$config      = [
    'controllers'     => [
        'initializers' => [
            ControllerInitializer::class
        ],
        'invokables'   => [
            'program'         => 'Program\Controller\ProgramController',
            'program-manager' => 'Program\Controller\ProgramManagerController',
            'program-nda'     => 'Program\Controller\NdaController',
            'program-doa'     => 'Program\Controller\DoaController',
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php', ], 'view_helpers' => [ 'invokables' => [ 'programHandler' => 'Program\View\Helper\ProgramHandler', 'callServiceProxy' => 'Program\View\Helper\CallServiceProxy', 'programServiceProxy' => 'Program\View\Helper\ProgramServiceProxy', 'callInformationBox' => 'Program\View\Helper\CallInformationBox', 'programLink' => 'Program\View\Helper\ProgramLink', 'programDoaLink' => 'Program\View\Helper\DoaLink', 'callLink' => 'Program\View\Helper\CallLink', 'ndaLink' => 'Program\View\Helper\NdaLink', ] ], 'service_manager' => [ 'initializers' => [ ServiceInitializer::class ], 'factories' => [ 'program_module_options' => 'Program\Service\OptionServiceFactory', 'program_nda_navigation_service' => 'Program\Navigation\Factory\NdaNavigationServiceFactory', ], 'invokables' => [ NdaAssertion::class => NdaAssertion::class, DoaAssertion::class => DoaAssertion::class, ProgramService::class => ProgramService::class, CallService::class => CallService::class, FormService::class => FormService::class, 'program_program_form_filter' => 'Program\Form\FilterCreateObject' ] ], 'doctrine' => [ 'driver' => [ 'program_annotation_driver' => [ 'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver', 'paths' => [ __DIR__ . '/../src/Program/Entity/' ] ], 'orm_default' => [ 'drivers' => [ 'Program\Entity' => 'program_annotation_driver', ] ] ], 'eventmanager' => [ 'orm_default' => [ 'subscribers' => [ 'Gedmo\Timestampable\TimestampableListener', 'Gedmo\Sluggable\SluggableListener', ] ], ], ] ];
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

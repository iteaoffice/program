<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
use Program\Acl;
use Program\Controller;
use Program\Factory;
use Program\InputFilter;
use Program\Navigation;
use Program\Options;
use Program\Service;
use Program\View;
use Zend\Stdlib;

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
            'getProgramFilter'          => Controller\Plugin\GetFilter::class,
            'renderNda'                 => Controller\Plugin\RenderNda::class,
            'renderProgramDoa'          => Controller\Plugin\RenderDoa::class,
            'renderSession'             => Controller\Plugin\RenderSession::class,
            'createCallFundingOverview' => Controller\Plugin\CreateCallFundingOverview::class,
            'createFundingDownload'     => Controller\Plugin\CreateFundingDownload::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                 => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderNda::class                 => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderDoa::class                 => Controller\Factory\PluginFactory::class,
            Controller\Plugin\RenderSession::class             => Controller\Factory\PluginFactory::class,
            Controller\Plugin\CreateCallFundingOverview::class => Controller\Factory\PluginFactory::class,
            Controller\Plugin\CreateFundingDownload::class     => Controller\Factory\PluginFactory::class,
        ],
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
        ],
    ],
    'service_manager'    => [
        'factories' => [
            Service\ProgramService::class              => Factory\ProgramServiceFactory::class,
            Service\CallService::class                 => Factory\CallServiceFactory::class,
            Service\FormService::class                 => Factory\FormServiceFactory::class,
            InputFilter\Call\CallFilter::class         => Factory\InputFilterFactory::class,
            InputFilter\Call\CountryFilter::class      => Factory\InputFilterFactory::class,
            InputFilter\DoaFilter::class               => Factory\InputFilterFactory::class,
            InputFilter\FunderFilter::class            => Factory\InputFilterFactory::class,
            InputFilter\ProgramFilter::class           => Factory\InputFilterFactory::class,
            Options\ModuleOptions::class               => Factory\ModuleOptionsFactory::class,
            Acl\Assertion\Doa::class                   => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Funder::class                => Acl\Factory\AssertionFactory::class,
            Acl\Assertion\Nda::class                   => Acl\Factory\AssertionFactory::class,
            Navigation\Invokable\CallLabel::class      => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\CountryLabel::class   => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\FunderLabel::class    => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\NdaLabel::class       => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\ProgramLabel::class   => Navigation\Factory\NavigationInvokableFactory::class,
            Navigation\Invokable\UploadNdaLabel::class => Navigation\Factory\NavigationInvokableFactory::class,
        ],
    ],
    'doctrine'           => [
        'driver'       => [
            'program_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Entity/'],
            ],
            'orm_default'               => [
                'drivers' => [
                    'Program\Entity' => 'program_annotation_driver',
                ],
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                ],
            ],
        ],
    ],
];
foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;

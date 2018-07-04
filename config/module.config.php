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
use Program\Factory\InvokableFactory;
use Program\InputFilter;
use Program\Navigation;
use Program\Options;
use Program\Service;
use Program\View;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\Stdlib;

$config = [
    'controllers'        => [
        'factories' => [
            Controller\CallCountryManagerController::class => ConfigAbstractFactory::class,
            Controller\CallManagerController::class        => ConfigAbstractFactory::class,
            Controller\DoaController::class                => ConfigAbstractFactory::class,
            Controller\FunderManagerController::class      => ConfigAbstractFactory::class,
            Controller\NdaController::class                => ConfigAbstractFactory::class,
            Controller\NdaManagerController::class         => ConfigAbstractFactory::class,
            Controller\ProgramManagerController::class     => ConfigAbstractFactory::class,
            Controller\SessionController::class            => ConfigAbstractFactory::class,
            Controller\SessionManagerController::class     => ConfigAbstractFactory::class,
            Controller\CallController::class               => ConfigAbstractFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getProgramFilter'          => Controller\Plugin\GetFilter::class,
            'renderNda'                 => Controller\Plugin\RenderNda::class,
            'renderProgramDoa'          => Controller\Plugin\RenderDoa::class,
            'sessionPdf'                => Controller\Plugin\SessionPdf::class,
            'sessionSpreadsheet'        => Controller\Plugin\SessionSpreadsheet::class,
            'createCallFundingOverview' => Controller\Plugin\CreateCallFundingOverview::class,
            'createFundingDownload'     => Controller\Plugin\CreateFundingDownload::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderNda::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderDoa::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\SessionPdf::class                => ConfigAbstractFactory::class,
            Controller\Plugin\SessionSpreadsheet::class        => ConfigAbstractFactory::class,
            Controller\Plugin\CreateCallFundingOverview::class => ConfigAbstractFactory::class,
            Controller\Plugin\CreateFundingDownload::class     => ConfigAbstractFactory::class,
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
            View\Handler\SessionHandler::class    => ConfigAbstractFactory::class,
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
        'factories'  => [
            Service\ProgramService::class => ConfigAbstractFactory::class,
            Service\CallService::class    => ConfigAbstractFactory::class,
            Service\FormService::class    => Factory\FormServiceFactory::class,

            InputFilter\ProgramFilter::class => ConfigAbstractFactory::class,

            Options\ModuleOptions::class      => Factory\ModuleOptionsFactory::class,
            Acl\Assertion\Doa::class          => InvokableFactory::class,
            Acl\Assertion\Funder::class       => InvokableFactory::class,
            Acl\Assertion\Nda::class          => InvokableFactory::class,
            Acl\Assertion\Call\Country::class => InvokableFactory::class,

            Navigation\Service\CallNavigationService::class => Navigation\Factory\CallNavigationServiceFactory::class,
            Navigation\Invokable\CallLabel::class           => InvokableFactory::class,
            Navigation\Invokable\CountryLabel::class        => InvokableFactory::class,
            Navigation\Invokable\FunderLabel::class         => InvokableFactory::class,
            Navigation\Invokable\NdaLabel::class            => InvokableFactory::class,
            Navigation\Invokable\ProgramLabel::class        => InvokableFactory::class,
            Navigation\Invokable\SessionLabel::class        => InvokableFactory::class,
            Navigation\Invokable\UploadNdaLabel::class      => InvokableFactory::class,
        ],
        'invokables' => [
            InputFilter\Call\CallFilter::class,
            InputFilter\Call\CountryFilter::class,
            InputFilter\DoaFilter::class,
            InputFilter\FunderFilter::class,
        ]
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

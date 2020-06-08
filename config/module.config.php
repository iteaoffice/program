<?php

/**
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use General\Navigation\Factory\NavigationInvokableFactory;
use General\View\Factory\LinkHelperFactory;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;
use Program\Acl;
use Program\Controller;
use Program\Factory;
use Program\Factory\InvokableFactory;
use Program\Form;
use Program\InputFilter;
use Program\Navigation;
use Program\Options;
use Program\Service;
use Program\View;

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
            Controller\CallController::class               => ConfigAbstractFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'getProgramFilter'          => Controller\Plugin\GetFilter::class,
            'renderNda'                 => Controller\Plugin\RenderNda::class,
            'renderDoa'                 => Controller\Plugin\RenderDoa::class,
            'callSizeSpreadsheet'       => Controller\Plugin\CallSizeSpreadsheet::class,
            'createCallFundingOverview' => Controller\Plugin\CreateCallFundingOverview::class,
            'createFundingDownload'     => Controller\Plugin\CreateFundingDownload::class,
        ],
        'factories' => [
            Controller\Plugin\GetFilter::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderNda::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\RenderDoa::class                 => ConfigAbstractFactory::class,
            Controller\Plugin\CallSizeSpreadsheet::class       => ConfigAbstractFactory::class,
            Controller\Plugin\CreateCallFundingOverview::class => ConfigAbstractFactory::class,
            Controller\Plugin\CreateFundingDownload::class     => ConfigAbstractFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'       => [
        'aliases'   => [

            'programHandler'     => View\Handler\ProgramHandler::class,
            'callInformationBox' => View\Helper\CallInformationBox::class,
            'programLink'        => View\Helper\ProgramLink::class,
            'programDoaLink'     => View\Helper\DoaLink::class,
            'callLink'           => View\Helper\CallLink::class,
            'ndaLink'            => View\Helper\NdaLink::class,
            'funderLink'         => View\Helper\FunderLink::class,
            'callCountryLink'    => View\Helper\CallCountryLink::class,
            'callformelement' => Form\View\Helper\CallFormElement::class,
        ],
        'factories' => [
            View\Handler\ProgramHandler::class      => ConfigAbstractFactory::class,
            View\Helper\ProgramLink::class          => LinkHelperFactory::class,
            View\Helper\DoaLink::class              => LinkHelperFactory::class,
            View\Helper\CallLink::class             => LinkHelperFactory::class,
            View\Helper\NdaLink::class              => LinkHelperFactory::class,
            View\Helper\FunderLink::class           => LinkHelperFactory::class,
            View\Helper\CallCountryLink::class      => LinkHelperFactory::class,
            View\Helper\CallInformationBox::class   => ConfigAbstractFactory::class,
            Form\View\Helper\CallFormElement::class => ConfigAbstractFactory::class
        ],
    ],
    'service_manager'    => [
        'factories'  => [
            Service\ProgramService::class                   => ConfigAbstractFactory::class,
            Service\CallService::class                      => ConfigAbstractFactory::class,
            Service\FormService::class                      => Factory\FormServiceFactory::class,
            InputFilter\DoaFilter::class                    => Factory\InputFilterFactory::class,
            InputFilter\ProgramFilter::class                => Factory\InputFilterFactory::class,
            InputFilter\FunderFilter::class                 => Factory\InputFilterFactory::class,
            InputFilter\Call\CallFilter::class              => Factory\InputFilterFactory::class,
            InputFilter\Call\CountryFilter::class           => Factory\InputFilterFactory::class,
            Options\ModuleOptions::class                    => Factory\ModuleOptionsFactory::class,
            Acl\Assertion\Doa::class                        => InvokableFactory::class,
            Acl\Assertion\Funder::class                     => InvokableFactory::class,
            Acl\Assertion\Nda::class                        => InvokableFactory::class,
            Acl\Assertion\Call\Country::class               => InvokableFactory::class,
            Navigation\Service\CallNavigationService::class => Navigation\Factory\CallNavigationServiceFactory::class,
            Navigation\Invokable\CallLabel::class           => NavigationInvokableFactory::class,
            Navigation\Invokable\CountryLabel::class        => NavigationInvokableFactory::class,
            Navigation\Invokable\FunderLabel::class         => NavigationInvokableFactory::class,
            Navigation\Invokable\NdaLabel::class            => NavigationInvokableFactory::class,
            Navigation\Invokable\ProgramLabel::class        => NavigationInvokableFactory::class,
            Navigation\Invokable\UploadNdaLabel::class      => NavigationInvokableFactory::class,
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
                'class' => AnnotationDriver::class,
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
                    TimestampableListener::class,
                    SluggableListener::class,
                ],
            ],
        ],
    ],
];
foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;

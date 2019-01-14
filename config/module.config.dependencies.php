<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2018 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program;

use Admin\Service\AdminService;
use Affiliation\Service\AffiliationService;
use Application\Service\AssertionService;
use BjyAuthorize\Service\Authorize;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Event\Service\MeetingService;
use Event\Service\RegistrationService;
use General\Service\CountryService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\EvaluationService;
use Project\Service\HelpService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use ZfcTwig\View\TwigRenderer;


return [
    ConfigAbstractFactory::class => [
        Controller\Plugin\CreateCallFundingOverview::class => [
            CountryService::class,
            VersionService::class,
            ProjectService::class,
            EvaluationService::class,
            AffiliationService::class
        ],
        Controller\Plugin\CreateFundingDownload::class     => [
            VersionService::class,
            ProjectService::class,
            AffiliationService::class
        ],
        Controller\Plugin\GetFilter::class                 => [
            'Application'
        ],
        Controller\Plugin\RenderDoa::class                 => [
            TwigRenderer::class,
            ModuleOptions::class,
            ContactService::class
        ],
        Controller\Plugin\RenderNda::class                 => [
            TwigRenderer::class,
            ModuleOptions::class,
            ContactService::class
        ],
        Controller\Plugin\SessionPdf::class                => [
            ModuleOptions::class,
            TranslatorInterface::class
        ],
        Controller\Plugin\SessionSpreadsheet::class        => [
            AssertionService::class,
            Authorize::class,
            TranslatorInterface::class,
            'ViewHelperManager'
        ],
        Controller\Plugin\SessionDocument::class           => [
            EntityManager::class,
            ModuleOptions::class,
            AssertionService::class,
            Authorize::class,
            TranslatorInterface::class,
            'ViewHelperManager'
        ],
        Controller\CallController::class                   => [
            CallService::class,
            ProjectService::class,
            IdeaService::class,
            HelpService::class,
            MeetingService::class,
            RegistrationService::class
        ],
        Controller\CallCountryManagerController::class     => [
            CallService::class,
            GeneralService::class,
            FormService::class,
            TranslatorInterface::class
        ],
        Controller\CallManagerController::class            => [
            CallService::class,
            FormService::class,
            ProjectService::class,
            VersionService::class,
            GeneralService::class,
            CountryService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\DoaController::class                    => [
            ProgramService::class,
            OrganisationService::class,
            GeneralService::class,
            TranslatorInterface::class
        ],
        Controller\FunderManagerController::class          => [
            ProgramService::class,
            FormService::class,
            TranslatorInterface::class
        ],
        Controller\NdaController::class                    => [
            ProgramService::class,
            CallService::class,
            GeneralService::class,
            ContactService::class,
            TranslatorInterface::class,
            TwigRenderer::class
        ],
        Controller\NdaManagerController::class             => [
            CallService::class,
            FormService::class,
            ContactService::class,
            GeneralService::class,
            AdminService::class,
            EmailService::class,
            TranslatorInterface::class,
            EntityManager::class
        ],
        Controller\ProgramManagerController::class         => [
            ProgramService::class,
            CallService::class,
            ProjectService::class,
            VersionService::class,
            FormService::class,
            EntityManager::class,
            TranslatorInterface::class
        ],
        Controller\SessionManagerController::class         => [
            ProgramService::class,
            IdeaService::class,
            FormService::class,
            TranslatorInterface::class,
            EntityManager::class
        ],
        Controller\SessionController::class                => [
            ProgramService::class,
            AssertionService::class
        ],
        InputFilter\ProgramFilter::class                   => [
            EntityManager::class
        ],
        Service\ProgramService::class                      => [
            EntityManager::class,
            OrganisationSearchService::class
        ],
        Service\CallService::class                         => [
            EntityManager::class,
            GeneralService::class,
            AdminService::class
        ],
        View\Handler\SessionHandler::class                 => [
            'Application',
            'ViewHelperManager',
            TwigRenderer::class,
            AuthenticationService::class,
            TranslatorInterface::class,
            ProgramService::class,
            IdeaService::class
        ],
        View\Handler\ProgramHandler::class                 => [
            'Application',
            'ViewHelperManager',
            TwigRenderer::class,
            AuthenticationService::class,
            TranslatorInterface::class,
            ProgramService::class
        ],
        View\Helper\CallInformationBox::class              => [
            CallService::class
        ]
    ]
];
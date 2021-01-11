<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program;

use Admin\Service\AdminService;
use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Evaluation\Service\EvaluationService;
use Event\Service\MeetingService;
use Event\Service\RegistrationService;
use General\Search\Service\CountrySearchService;
use General\Service\CountryService;
use General\Service\EmailService;
use General\Service\GeneralService;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\RouteStackInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Organisation\Search\Service\OrganisationSearchService;
use Organisation\Service\OrganisationService;
use Program\Form\View\Helper\CallFormElement;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Search\Service\ProjectSearchService;
use Project\Service\ContractService;
use Project\Service\HelpService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
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
        Controller\Plugin\CallSizeSpreadsheet::class       => [
            ProjectService::class,
            VersionService::class,
            AffiliationService::class,
            ContractService::class,
            ContactService::class,
            CountryService::class,
            EntityManager::class,
            TranslatorInterface::class
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
            AffiliationService::class,
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
            FormService::class,
            TranslatorInterface::class
        ],
        Service\ProgramService::class                      => [
            EntityManager::class,
            OrganisationSearchService::class,
            ProjectSearchService::class,
            CountrySearchService::class,
            ContactService::class
        ],
        Service\CallService::class                         => [
            EntityManager::class,
            GeneralService::class,
            AdminService::class
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
            CallService::class,
            TranslatorInterface::class,
            RouteStackInterface::class,
        ],
        CallFormElement::class                             => [
            'ViewHelperManager',
            TranslatorInterface::class
        ],
    ]
];

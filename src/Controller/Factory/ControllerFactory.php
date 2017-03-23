<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Controller\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\EmailService;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Program\Controller\ProgramAbstractController;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\HelperPluginManager;

/**
 * Class ControllerFactory
 *
 * @package Program\Controller\Factory
 */
final class ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ControllerManager $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return ProgramAbstractController
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): ProgramAbstractController {
        /** @var ProgramAbstractController $controller */
        $controller = new $requestedName($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

        /** @var FormService $formService */
        $formService = $container->get(FormService::class);
        $controller->setFormService($formService);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var VersionService $versionService */
        $versionService = $container->get(VersionService::class);
        $controller->setVersionService($versionService);

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var ProgramService $programService */
        $programService = $container->get(ProgramService::class);
        $controller->setProgramService($programService);

        /** @var CallService $callService */
        $callService = $container->get(CallService::class);
        $controller->setCallService($callService);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $controller->setAdminService($adminService);

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);
        $controller->setModuleOptions($moduleOptions);

        /** @var EmailService $emailService */
        $emailService = $container->get(EmailService::class);
        $controller->setEmailService($emailService);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $controller->setViewHelperManager($viewHelperManager);

        return $controller;
    }
}

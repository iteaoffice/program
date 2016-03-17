<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Publication
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2016 ITEA Office (http://itea3.org)
 */

namespace Program\Controller\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Controller\ProgramAbstractController;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ControllerInvokableAbstractFactory
 *
 * @package Program\Controller\Factory
 */
class ControllerInvokableAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (class_exists($requestedName)
            && in_array(ProgramAbstractController::class, class_parents($requestedName)));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface|ControllerManager $serviceLocator
     * @param string                                    $name
     * @param string                                    $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /** @var ProgramAbstractController $controller */
        $controller = new $requestedName();
        $controller->setServiceLocator($serviceLocator);

        $serviceManager = $serviceLocator->getServiceLocator();

        /** @var FormService $formService */
        $formService = $serviceManager->get(FormService::class);
        $controller->setFormService($formService);

        /** @var ContactService $contactService */
        $contactService = $serviceManager->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var GeneralService $generalService */
        $generalService = $serviceManager->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ProjectService $projectService */
        $projectService = $serviceManager->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $serviceManager->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var ProgramService $programService */
        $programService = $serviceManager->get(ProgramService::class);
        $controller->setProgramService($programService);

        /** @var CallService $callService */
        $callService = $serviceManager->get(CallService::class);
        $controller->setCallService($callService);

        /** @var AdminService $adminService */
        $adminService = $serviceManager->get(AdminService::class);
        $controller->setAdminService($adminService);

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceManager->get(ModuleOptions::class);
        $controller->setModuleOptions($moduleOptions);

        return $controller;
    }
}

<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */
namespace Program\Controller\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Program\Controller\ProgramAbstractController;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\EvaluationService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ControllerFactory
 *
 * @package Program\Controller\Factory
 */
final class ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ControllerManager $container
     * @param string                               $requestedName
     * @param array|null                           $options
     *
     * @return ProgramAbstractController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ProgramAbstractController $controller */
        $controller = new $requestedName($options);

        $serviceManager = $container->getServiceLocator();

        /** @var EntityManager $entityManager */
        $entityManager = $serviceManager->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

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

        /** @var VersionService $versionService */
        $versionService = $serviceManager->get(VersionService::class);
        $controller->setVersionService($versionService);

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

        /** @var EvaluationService $evaluationService */
        $evaluationService = $serviceManager->get(EvaluationService::class);
        $controller->setEvaluationService($evaluationService);

        return $controller;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param string                  $canonicalName
     * @param string                  $requestedName
     *
     * @return ProgramAbstractController
     */
    public function createService(ServiceLocatorInterface $container, $canonicalName = null, $requestedName = null)
    {
        return $this($container, $requestedName);
    }
}

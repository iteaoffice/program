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
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Program\Service\CallService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CallServiceFactory
 *
 * @package Call\Factory
 */
class CallServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CallService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $callService = new CallService();
        $callService->setServiceLocator($serviceLocator);

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get(EntityManager::class);
        $callService->setEntityManager($entityManager);

        /** @var GeneralService $generalService */
        $generalService = $serviceLocator->get(GeneralService::class);
        $callService->setGeneralService($generalService);

        /** @var VersionService $versionService */
        $versionService = $serviceLocator->get(VersionService::class);
        $callService->setVersionService($versionService);

//        /** @var ProjectService $projectService */
//        $projectService = $serviceLocator->get(ProjectService::class);
//        $callService->setProjectService($projectService);

        return $callService;
    }
}

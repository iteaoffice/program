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

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\EntityManager;
use Program\Service\ProgramService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ProgramServiceFactory
 *
 * @package Program\Factory
 */
class ProgramServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ProgramService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $programService = new ProgramService();
        $programService->setServiceLocator($serviceLocator);

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get(EntityManager::class);
        $programService->setEntityManager($entityManager);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $serviceLocator->get(AffiliationService::class);
        $programService->setAffiliationService($affiliationService);

        return $programService;
    }
}

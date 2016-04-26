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
use Interop\Container\ContainerInterface;
use Program\Service\ProgramService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ProgramServiceFactory
 *
 * @package Program\Factory
 */
final class ProgramServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ProgramService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $programService = new ProgramService($options);
        $programService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $programService->setEntityManager($entityManager);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $container->get(AffiliationService::class);
        $programService->setAffiliationService($affiliationService);

        return $programService;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string|null             $canonicalName
     * @param string|null             $requestedName
     *
     * @return ProgramService
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}

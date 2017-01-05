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
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Program\Service\CallService;
use Project\Service\VersionService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FormFactory
 *
 * @package Content\Factory
 */
final class CallServiceFactory implements FactoryInterface
{
    /**
     * Create an instance of the requested class name.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return CallService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CallService
    {
        /** @var CallService $callService */
        $callService = new $requestedName($options);
        $callService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $callService->setEntityManager($entityManager);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $callService->setGeneralService($generalService);

        /** @var VersionService $versionService */
        $versionService = $container->get(VersionService::class);
        $callService->setVersionService($versionService);

        return $callService;
    }
}

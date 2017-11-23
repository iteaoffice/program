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
declare(strict_types=1);

namespace Program\Factory;

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Program\Service\ProgramService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ProgramServiceFactory
 *
 * @package Program\Factory
 */
final class ProgramServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ProgramService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProgramService
    {
        /** @var ProgramService $programService */
        $programService = new $requestedName($options);
        $programService->setServiceLocator($container);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $programService->setEntityManager($entityManager);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $container->get(AffiliationService::class);
        $programService->setAffiliationService($affiliationService);

        return $programService;
    }
}

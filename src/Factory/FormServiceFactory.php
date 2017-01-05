<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    General
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Program\Service\FormService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FormServiceFactory
 *
 * @package General\Factory
 */
final class FormServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return FormService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formService = new FormService($options);
        $formService->setServiceLocator($container);
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $formService->setEntityManager($entityManager);

        return $formService;
    }
}

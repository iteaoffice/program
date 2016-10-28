<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FormFactory
 *
 * @package Program\Factory
 */
final class FormFactory implements FactoryInterface
{
    /**
     * Create an instance of the requested class name.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container->get(EntityManager::class), $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string|null             $canonicalName
     * @param string|null             $requestedName
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}

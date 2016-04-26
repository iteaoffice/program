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
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InputFilterFactory
 *
 * @package Content\Factory
 */
final class InputFilterFactory implements FactoryInterface
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
     * @param null|string             $canonicalName
     * @param null|string             $requestedName
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}

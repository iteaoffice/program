<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Factory;

use Admin\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Program\Navigation\Service\CallNavigationService;
use Program\Service\CallService;
use Zend\Mvc\Application;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class CallNavigationServiceFactory
 *
 * @package Program\Navigation\Factory
 */
final class CallNavigationServiceFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): CallNavigationService {
        /** @var Application $application */
        $application = $container->get('application');

        /** @var ModuleOptions $adminModuleOptions */
        $adminModuleOptions = $container->get(ModuleOptions::class);

        return new CallNavigationService(
            $container->get($adminModuleOptions->getCommunityNavigationContainer()),
            $application->getMvcEvent()->getRouteMatch(),
            $container->get(CallService::class)
        );
    }
}

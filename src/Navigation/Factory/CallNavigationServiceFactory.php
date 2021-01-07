<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Navigation\Factory;

use General\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Program\Navigation\Service\CallNavigationService;
use Program\Service\CallService;
use Project\Service\IdeaService;

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
            $application->getMvcEvent()->getRouter(),
            $application->getMvcEvent()->getRouteMatch(),
            $container->get(CallService::class),
            $container->get(IdeaService::class)
        );
    }
}

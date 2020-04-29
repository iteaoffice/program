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

namespace Program;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Program\Navigation\Service\CallNavigationService;

/**
 * Class Module
 *
 * @package Program
 */
final class Module implements Feature\ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        /**
         * @var $app Application
         */
        $app = $e->getParam('application');
        $em  = $app->getEventManager();
        $em->attach(
            MvcEvent::EVENT_RENDER,
            static function (MvcEvent $event) {
                $event->getApplication()->getServiceManager()->get(CallNavigationService::class)();
            }
        );
    }
}

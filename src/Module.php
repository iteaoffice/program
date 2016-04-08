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

namespace Program;

use Program\Controller\Plugin;
use Program\Controller\Plugin\RenderDoa;
use Program\Controller\Plugin\RenderNda;
use Program\Controller\Plugin\RenderSession;
use Zend\ModuleManager\Feature;
use Zend\Mvc\Controller\PluginManager;

/**
 *
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\ControllerPluginProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../autoload_classmap.php',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Move this to here to have config cache working.
     *
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return [
            'invokables' => [
                'getProgramFilter' => Plugin\GetFilter::class,
            ],
            'factories'  => [
                'renderNda'        => function (PluginManager $sm) {
                    $renderNda = new RenderNda();
                    $renderNda->setServiceLocator($sm->getServiceLocator());

                    return $renderNda;
                },
                'renderProgramDoa' => function (PluginManager $sm) {
                    $renderDoa = new RenderDoa();
                    $renderDoa->setServiceLocator($sm->getServiceLocator());

                    return $renderDoa;
                },
                'renderSession'    => function (PluginManager $sm) {
                    $renderSession = new RenderSession();
                    $renderSession->setServiceLocator($sm->getServiceLocator());

                    return $renderSession;
                },
            ],
        ];
    }
}

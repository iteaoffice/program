<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    SoloDB
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 *
 * @version     4.0
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
class Module implements Feature\AutoloaderProviderInterface, Feature\ServiceProviderInterface, Feature\ConfigProviderInterface
{
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Go to the service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/services.config.php';
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

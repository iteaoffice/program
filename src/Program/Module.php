<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    SoloDB
 * @package     Program
 * @subpackage  Module
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 * @version     4.0
 */
namespace Program;

use Program\Controller\Plugin\RenderDoa;
use Program\Controller\Plugin\RenderNda;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

/**
 *
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ServiceProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__.'/../../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__.'/../../src/'.__NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__.'/../../config/module.config.php';
    }

    /**
     * Go to the service configuration
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__.'/../../config/services.config.php';
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return include __DIR__.'/../../config/viewhelpers.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
    }

    /**
     * Move this to here to have config cache working
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'renderNda'        => function ($sm) {
                    $renderNda = new RenderNda();
                    $renderNda->setServiceLocator($sm->getServiceLocator());

                    return $renderNda;
                },
                'renderProgramDoa' => function ($sm) {
                    $renderDoa = new RenderDoa();
                    $renderDoa->setServiceLocator($sm->getServiceLocator());

                    return $renderDoa;
                },
            ],
        ];
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getParam('application');
        $em = $app->getEventManager();
        $em->attach(
            MvcEvent::EVENT_DISPATCH,
            function ($event) {
                $event->getApplication()->getServiceManager()->get('program_nda_navigation_service')->update();
            }
        );
    }
}

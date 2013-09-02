<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    SoloDB
 * @package     Program
 * @subpackage  Module
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 * @version     4.0
 */
namespace Program;

use Zend\ModuleManager\Feature; //Makes the module class more strict
use Zend\EventManager\EventInterface;

use Program\Service\FormServiceAwareInterface;

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
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Go to the service configuration
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/services.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof FormServiceAwareInterface) {
                        $sm          = $sm->getServiceLocator();
                        $formService = $sm->get('program_form_service');
                        $instance->setFormService($formService);
                    }
                },
            ),
        );
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
        // TODO: Implement onBootstrap() method.
    }
}

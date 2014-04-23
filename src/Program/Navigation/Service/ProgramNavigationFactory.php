<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Navigation
 * @subpackage  Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Mvc\Router\Http\RouteMatch;

use Program\Service\ProgramService;

/**
 * Factory for the Project admin navigation
 *
 * @package    Program
 * @subpackage Navigation\Service
 */
class ProgramNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var ProgramService;
     */
    protected $programService;


    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param array                   $pages
     *
     * @return array
     */
    public function getExtraPages(ServiceLocatorInterface $serviceLocator, array $pages)
    {
        $application          = $serviceLocator->get('Application');
        $this->routeMatch     = $application->getMvcEvent()->getRouteMatch();
        $router               = $application->getMvcEvent()->getRouter();
        $this->programService = $serviceLocator->get('program_program_service');
        $translate            = $serviceLocator->get('viewhelpermanager')->get('translate');

        /**
         * Return $pages when no match is found
         */
        if (is_null($this->routeMatch)) {
            return $pages;
        }

        /**
         * Go over the routes to see if we need to extend the $this->pages array
         */

        switch ($this->routeMatch->getMatchedRouteName()) {

            case 'program/nda/upload':

                $pages['community'] = array(
                    'label'      => $translate("txt-account-information"),
                    'route'      => 'contact/profile',
                    'routeMatch' => $this->routeMatch,
                    'router'     => $router,
                );

                if (!is_null($this->routeMatch->getParam('call-id'))) {
                    $call = $this->programService->findEntityById('Call\Call', $this->routeMatch->getParam('call-id'));
                    /**
                     * Go over both arrays and check if the new entities can be added
                     */
                    $pages['community']['pages']['nda'] = array(
                        'label'      => sprintf($translate("txt-upload-nda-for-call-%s"), $call),
                        'route'      => 'program/nda/upload',
                        'routeMatch' => $this->routeMatch,
                        'router'     => $router,
                        'active'     => true,
                        'params'     => array(
                            'call-id' => $this->routeMatch->getParam('call-id')
                        )
                    );
                }
                break;
        }


        return $pages;
    }
}

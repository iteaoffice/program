<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Calendar
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Navigation\Factory;

use Program\Navigation\Service\NdaNavigationService;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Navigation\Navigation;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * NodeService
 *
 * this is a wrapper for node entity related services
 *
 */
class NdaNavigationServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return array|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $ndaNavigationService = new NdaNavigationService();
        $ndaNavigationService->setTranslator($serviceLocator->get('viewhelpermanager')->get('translate'));
        /**
         * @var $programService ProgramService
         */
        $programService = $serviceLocator->get(ProgramService::class);
        $ndaNavigationService->setProgramService($programService);
        /**
         * @var $callService CallService
         */
        $callService = $serviceLocator->get(CallService::class);
        $ndaNavigationService->setCallService($callService);
        $application = $serviceLocator->get('application');
        $ndaNavigationService->setRouteMatch($application->getMvcEvent()->getRouteMatch());
        $ndaNavigationService->setRouter($application->getMvcEvent()->getRouter());
        /**
         * @var $navigation Navigation
         */
        $navigation = $serviceLocator->get('navigation');
        $ndaNavigationService->setNavigation($navigation);

        return $ndaNavigationService;
    }
}

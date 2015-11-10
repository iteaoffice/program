<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Navigation\Service;

use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Navigation\Navigation;

/**
 * Factory for the Program admin navigation.
 */
class NavigationServiceAbstract
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var Translate
     */
    protected $translator;
    /**
     * @var ProgramService;
     */
    protected $programService;
    /**
     * @var CallService;
     */
    protected $callService;
    /**
     * @var TreeRouteStack
     */
    protected $router;
    /**
     * @var Navigation
     */
    protected $navigation;
    /**
     * @var Navigation
     */
    protected $cmsNavigation;

    /**
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param Navigation $navigation
     *
     * @return NavigationServiceAbstract
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @return Navigation
     */
    public function getCmsNavigation()
    {
        return $this->cmsNavigation;
    }

    /**
     * @param Navigation $cmsNavigation
     */
    public function setCmsNavigation($cmsNavigation)
    {
        $this->cmsNavigation = $cmsNavigation;
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     *
     * @return NavigationServiceAbstract
     */
    public function setRouteMatch($routeMatch)
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->getTranslator()->__invoke($string);
    }

    /**
     * @return Translate
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Translate $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TreeRouteStack
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param TreeRouteStack $router
     *
     * @return NavigationServiceAbstract;
     */
    public function setRouter($router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->programService;
    }

    /**
     * @param ProgramService $programService
     *
     * @return NavigationServiceAbstract
     */
    public function setProgramService($programService)
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->callService;
    }

    /**
     * @param CallService $callService
     *
     * @return NavigationServiceAbstract
     */
    public function setCallService($callService)
    {
        $this->callService = $callService;

        return $this;
    }
}

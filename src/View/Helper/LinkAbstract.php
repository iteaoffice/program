<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2015 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

namespace Program\View\Helper;

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\View\Helper\IsAllowed;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country as CallCountry;
use Program\Entity\Doa;
use Program\Entity\EntityAbstract;
use Program\Entity\Program;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;

/**
 * Create a link to an document.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */
abstract class LinkAbstract extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;
    /**
     * @var string Text to be placed as title or as part of the linkContent
     */
    protected $text;
    /**
     * @var string
     */
    protected $router;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $show;
    /**
     * @var string
     */
    protected $alternativeShow;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array content of the link (will be imploded during creation of the link)
     */
    protected $linkContent = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var array
     */
    protected $showOptions = [];
    /**
     * @var Doa
     */
    protected $doa;
    /**
     * @var Organisation
     */
    protected $organisation;
    /**
     * @var Program
     */
    protected $program;
    /**
     * @var Call
     */
    protected $call;
    /**
     * @var Country
     */
    protected $country;
    /**
     * @var CallCountry
     */
    protected $callCountry;
    /**
     * @var int
     */
    protected $page;

    /**
     * This function produces the link in the end.
     *
     * @return string
     */
    public function createLink()
    {
        /**
         * @var $url Url
         */
        $url = $this->serviceLocator->get('url');

        /**
         * @var $serverUrl ServerUrl
         */
        $serverUrl = $this->serviceLocator->get('serverUrl');
        $this->linkContent = [];
        $this->classes = [];
        $this->parseAction();
        $this->parseShow();

        if ('social' === $this->getShow()) {
            return $serverUrl() . $url($this->router, $this->routerParams);
        }
        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl() . $url($this->router, $this->routerParams),
            htmlentities($this->text),
            implode(' ', $this->classes),
            in_array($this->getShow(), ['icon', 'button', 'alternativeShow']) ? implode('', $this->linkContent)
            : htmlentities(implode('', $this->linkContent))
        );
    }

    /**
     * Default version of the action.
     */
    public function parseAction()
    {
        $this->action = null;
    }

    /**
     * @throws \Exception
     */
    public function parseShow()
    {
        switch ($this->getShow()) {
            case 'icon':
            case 'button':
                switch ($this->getAction()) {
                    case 'new':
                    case 'new-admin':
                        $this->addLinkContent('<i class="fa fa-plus"></i>');
                        break;
                    case 'list':
                        $this->addLinkContent('<i class="fa fa-list-ul"></i>');
                        break;
                    case 'edit':
                    case 'edit-admin':
                        $this->addLinkContent('<i class="fa fa-edit"></i>');
                        break;
                    case 'view':
                    case 'view-admin':
                        $this->addLinkContent('<i class="fa fa-external-link"></i>');
                        break;
                    case 'download':
                        $this->addLinkContent('<i class="fa fa-file-zip-o"></i>');
                        break;
                    default:
                        $this->addLinkContent('<i class="fa fa-file-o"></i>');
                        break;
                }

                if ($this->getShow() === 'button') {
                    $this->addLinkContent(' ' . $this->getText());
                    if ($this->getAction() === 'delete') {
                        $this->addClasses("btn btn-danger");
                    } else {
                        $this->addClasses("btn btn-primary");
                    }
                }
                break;
            case 'text':
                $this->addLinkContent($this->getText());
                break;
            case 'paginator':
                if (is_null($this->getAlternativeShow())) {
                    throw new \InvalidArgumentException(sprintf("this->alternativeShow cannot be null for a paginator link"));
                }
                $this->addLinkContent($this->getAlternativeShow());
                break;
            case 'social':
                /*
                 * Social is treated in the createLink function, no content needs to be created
                 */

                return;
            default:
                if (!array_key_exists($this->getShow(), $this->showOptions)) {
                    throw new \InvalidArgumentException(sprintf(
                        "The option \"%s\" should be available in the showOptions array, only \"%s\" are available",
                        $this->getShow(),
                        implode(', ', array_keys($this->showOptions))
                    ));
                }
                $this->addLinkContent($this->showOptions[$this->getShow()]);
                break;
        }
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param string $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param $linkContent
     *
     * @return $this
     */
    public function addLinkContent($linkContent)
    {
        if (!is_array($linkContent)) {
            $linkContent = [$linkContent];
        }
        foreach ($linkContent as $content) {
            $this->linkContent[] = $content;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = [$classes];
        }
        foreach ($classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternativeShow()
    {
        return $this->alternativeShow;
    }

    /**
     * @param string $alternativeShow
     */
    public function setAlternativeShow($alternativeShow)
    {
        $this->alternativeShow = $alternativeShow;
    }

    public function setShowOptions($showOptions)
    {
        $this->showOptions = $showOptions;
    }

    /**
     * @param EntityAbstract $entity
     * @param string         $assertion
     * @param string         $action
     *
     * @return bool
     */
    public function hasAccess(EntityAbstract $entity, $assertion, $action)
    {
        $assertion = $this->getAssertion($assertion);
        if (!is_null($entity) && !$this->getAuthorizeService()->getAcl()->hasResource($entity)) {
            $this->getAuthorizeService()->getAcl()->addResource($entity);
            $this->getAuthorizeService()->getAcl()->allow([], $entity, [], $assertion);
        }
        if (!$this->isAllowed($entity, $action)) {
            return false;
        }

        return true;
    }

    /**
     * @param $assertion
     *
     * @return mixed
     */
    public function getAssertion($assertion)
    {
        return $this->getServiceLocator()->get($assertion);
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return Authorize
     */
    public function getAuthorizeService()
    {
        return $this->getServiceLocator()->get('BjyAuthorize\Service\Authorize');
    }

    /**
     * @param null|EntityAbstract $resource
     * @param string              $privilege
     *
     * @return bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        /**
         * @var $isAllowed IsAllowed
         */
        $isAllowed = $this->serviceLocator->get('isAllowed');

        return $isAllowed($resource, $privilege);
    }

    /**
     * Add a parameter to the list of parameters for the router.
     *
     * @param string $key
     * @param        $value
     * @param bool   $allowNull
     */
    public function addRouterParam($key, $value, $allowNull = true)
    {
        if (!$allowNull && is_null($value)) {
            throw new \InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (!is_null($value)) {
            $this->routerParams[$key] = $value;
        }
    }

    /**
     * @return string
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param string $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getRouterParams()
    {
        return $this->routerParams;
    }

    /**
     * RouteInterface match returned by the router.
     * Use a test on is_null to have the possibility to overrule the serviceLocator lookup for unit tets reasons.
     *
     * @return RouteMatch.
     */
    public function getRouteMatch()
    {
        if (is_null($this->routeMatch)) {
            $this->routeMatch = $this->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
        }

        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->serviceLocator->get('translate')->__invoke($string);
    }

    /**
     * @return Doa
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param  Doa $doa
     *
     * @return LinkAbstract
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;

        return $this;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param  Organisation $organisation
     *
     * @return LinkAbstract
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * @return Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param  Program $program
     *
     * @return LinkAbstract
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return LinkAbstract
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        if (is_null($this->call)) {
            $this->call = new Call();
        }

        return $this->call;
    }

    /**
     * @param Call $call
     *
     * @return LinkAbstract
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        if (is_null($this->country)) {
            $this->country = new Country();
        }

        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return LinkAbstract
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return CallCountry
     */
    public function getCallCountry()
    {
        if (is_null($this->callCountry)) {
            $this->callCountry = new CallCountry();
        }

        return $this->callCountry;
    }

    /**
     * @param CallCountry $callCountry
     *
     * @return LinkAbstract
     */
    public function setCallCountry($callCountry)
    {
        $this->callCountry = $callCountry;

        return $this;
    }
}
<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2018 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\View\Helper\IsAllowed;
use Contact\Entity\Contact;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country as CallCountry;
use Program\Entity\Doa;
use Program\Entity\EntityAbstract;
use Program\Entity\Funder;
use Program\Entity\Nda;
use Program\Entity\Program;
use Zend\Router\Http\RouteMatch;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;

/**
 * Class AbstractLink
 * @package Program\View\Helper
 */
abstract class AbstractLink extends AbstractViewHelper
{
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
    protected $fragment = null;
    /**
     * @var array Url query params
     */
    protected $query = [];
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
     * @var Nda
     */
    protected $nda;
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var Funder
     */
    protected $funder;
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
     * @return string
     * @throws \Exception
     */
    public function createLink(): string
    {
        /** @var $url Url */
        $url = $this->getHelperPluginManager()->get('url');
        /** @var $serverUrl ServerUrl */
        $serverUrl = $this->getHelperPluginManager()->get('serverUrl');

        // Init params and layout
        $this->fragment = null;
        $this->query = [];
        $this->classes = [];
        $this->linkContent = [];

        $this->parseAction();
        $this->parseShow();

        if ('social' === $this->getShow()) {
            return $serverUrl() . $url(
                $this->router,
                $this->routerParams,
                ['query' => $this->query, 'fragment' => $this->fragment]
            );
        }
        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl() . $url(
                $this->router,
                $this->routerParams,
                ['query' => $this->query, 'fragment' => $this->fragment]
            ),
            htmlentities((string) $this->text),
            implode(' ', $this->classes),
            \in_array($this->getShow(), ['icon', 'button', 'alternativeShow']) ? implode('', $this->linkContent)
                : htmlentities(implode('', $this->linkContent))
        );
    }

    /**
     * Default version of the action.
     */
    public function parseAction(): void
    {
        $this->action = null;
    }

    /**
     * @throws \Exception
     */
    public function parseShow(): void
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
                        $this->addLinkContent('<i class="fa fa-download" aria-hidden="true"></i>');
                        break;
                    case 'upload':
                    case 'upload-admin':
                        $this->addLinkContent('<i class="fa fa-upload" aria-hidden="true"></i>');
                        break;
                    case 'render':
                        $this->addLinkContent('<i class="fa fa-file-pdf-o"></i>');
                        break;
                    case 'replace':
                        $this->addLinkContent('<i class="fa fa-refresh" aria-hidden="true"></i>');
                        break;
                    case 'funding':
                        $this->addLinkContent('<i class="fa fa-eur" aria-hidden="true"></i>');
                        break;
                    case 'download-funding':
                        $this->addLinkContent('<i class="fa fa-file-excel-o" aria-hidden="true"></i>');
                        break;
                    case 'size':
                        $this->addLinkContent('<i class="fa fa-signal" aria-hidden="true"></i>');
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
                        $this->addClasses('btn btn-primary');
                    }
                }
                break;
            case 'text':
                $this->addLinkContent($this->getText());
                break;
            case 'paginator':
                if (null === $this->getAlternativeShow()) {
                    throw new \InvalidArgumentException(
                        sprintf("this->alternativeShow cannot be null for a paginator link")
                    );
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
                    throw new \InvalidArgumentException(
                        sprintf(
                            "The option \"%s\" should be available in the showOptions array, only \"%s\" are available",
                            $this->getShow(),
                            implode(', ', array_keys($this->showOptions))
                        )
                    );
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
     * @param string|array $classes
     *
     * @return $this
     */
    public function addClasses($classes)
    {
        foreach ((array) $classes as $class) {
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

    /**
     * @param $showOptions
     */
    public function setShowOptions($showOptions)
    {
        $this->showOptions = $showOptions;
    }

    /**
     * @param EntityAbstract $entity
     * @param string $assertion
     * @param string $action
     *
     * @return bool
     */
    public function hasAccess(EntityAbstract $entity, $assertion, $action)
    {
        $assertion = $this->getAssertion($assertion);
        if (!\is_null($entity) && !$this->getAuthorizeService()->getAcl()->hasResource($entity)) {
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
        return $this->getServiceManager()->get($assertion);
    }

    /**
     * @return Authorize
     */
    public function getAuthorizeService(): Authorize
    {
        return $this->getServiceManager()->get('BjyAuthorize\Service\Authorize');
    }

    /**
     * @param null|EntityAbstract $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function isAllowed($resource, $privilege = null): bool
    {
        /**
         * @var $isAllowed IsAllowed
         */
        $isAllowed = $this->getHelperPluginManager()->get('isAllowed');

        return $isAllowed($resource, $privilege);
    }

    /**
     * Add a parameter to the list of parameters for the router.
     *
     * @param string $key
     * @param        $value
     * @param bool $allowNull
     */
    public function addRouterParam($key, $value, $allowNull = true): void
    {
        if (!$allowNull && null === $value) {
            throw new \InvalidArgumentException(sprintf("null is not allowed for %s", $key));
        }
        if (null !== $value) {
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
     * @return Doa
     */
    public function getDoa()
    {
        if (\is_null($this->doa)) {
            $this->doa = new Doa();
        }

        return $this->doa;
    }

    /**
     * @param  Doa $doa
     *
     * @return AbstractLink
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;

        return $this;
    }

    /**
     * @return Nda
     */
    public function getNda(): Nda
    {
        if (\is_null($this->nda)) {
            $this->nda = new Nda();
        }

        return $this->nda;
    }

    /**
     * @param  Nda $nda
     *
     * @return AbstractLink
     */
    public function setNda($nda): AbstractLink
    {
        $this->nda = $nda;

        return $this;
    }

    /**
     * @return Funder
     */
    public function getFunder()
    {
        if (\is_null($this->funder)) {
            $this->funder = new Funder();
        }

        return $this->funder;
    }

    /**
     * @param  Funder $funder
     *
     * @return AbstractLink
     */
    public function setFunder($funder)
    {
        $this->funder = $funder;

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
     * @return AbstractLink
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
     * @return AbstractLink
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
     * @return AbstractLink
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
        if (\is_null($this->call)) {
            $this->call = new Call();
        }

        return $this->call;
    }

    /**
     * @param Call $call
     *
     * @return AbstractLink
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
        if (\is_null($this->country)) {
            $this->country = new Country();
        }

        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return AbstractLink
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
        if (\is_null($this->callCountry)) {
            $this->callCountry = new CallCountry();
        }

        return $this->callCountry;
    }

    /**
     * @param CallCountry $callCountry
     *
     * @return AbstractLink
     */
    public function setCallCountry($callCountry)
    {
        $this->callCountry = $callCountry;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        if (\is_null($this->contact)) {
            $this->contact = new Contact();
        }

        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return AbstractLink
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }


}

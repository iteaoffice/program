<?php

/**
 * Jield BV all rights reserved
 *
 * @category    Application
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Program\Controller\Plugin;

use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Router\Http\RouteMatch;
use function base64_decode;
use function base64_encode;

/**
 * Class GetFilter
 *
 * @package Program\Controller\Plugin
 */
final class GetFilter extends AbstractPlugin
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    private array $filter = [];
    private ? string $query;

    public function __construct(Application $application)
    {
        $this->routeMatch = $application->getMvcEvent()->getRouteMatch();
        $this->request = $application->getMvcEvent()->getRequest();
    }

    public function __invoke() : self
    {
        $encodedFilter = urldecode((string)$this->routeMatch->getParam('encodedFilter'));

        $order = $this->request->getQuery('order');
        $direction = $this->request->getQuery('direction');

        //Take the filter from the URL
        $filter = [];
        if (!empty($base64decodedFilter = base64_decode($encodedFilter))) {
            $filter = (array)Json::decode($base64decodedFilter);
        }

        //If the form is submitted, refresh the URL
        if ($this->request->isGet() && null !== $this->request->getQuery('submit')) {
            $filter = $this->request->getQuery()->toArray()['filter'];
        }

        //Add a default order and direction if not known in the filter
        if (!isset($filter['order'])) {
            $filter['order'] = 'id';
            $filter['direction'] = 'desc';
        }

        //Overrule the order if set in the query
        if (null !== $order) {
            $filter['order'] = $order;
        }

        //Overrule the direction if set in the query
        if (null !== $direction) {
            $filter['direction'] = $direction;
        }

        $this->filter = $filter;

        return $this;
    }

    public function getHash(): string
    {
        return base64_encode(Json::encode($this->filter));
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getOrder(): ?string
    {
        return $this->filter['order'];
    }

    public function getDirection(): ?string
    {
        return $this->filter['direction'];
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }
}

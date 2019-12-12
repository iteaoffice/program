<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Service;

use Program\Service\CallService;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc;
use Zend\Navigation\Page\Uri;
use Zend\Router\RouteMatch;

/**
 * Class CallNavigationService
 *
 * @package Program\Navigation\Service
 */
final class CallNavigationService
{
    /**
     * @var Navigation
     */
    private $navigation;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    /**
     * @var CallService
     */
    private $callService;

    public function __construct(Navigation $navigation, ?RouteMatch $routeMatch, CallService $callService)
    {
        $this->navigation = $navigation->current();
        $this->routeMatch = $routeMatch;
        $this->callService = $callService;
    }

    public function __invoke(): void
    {
        if (null === $this->routeMatch) {
            return;
        }

        /** @var Mvc $ideaIndex */
        $callIndex = $this->navigation->findOneBy('id', 'callindex');

        if (null === $callIndex) {
            return;
        }

        $pages = $callIndex->getPages();

        $calls = $this->callService->findOpenCall();
        $showCalls = [];

        if ($calls->hasUpcoming()) {
            $showCalls[] = $calls->getUpcoming();
        }

        if (!$calls->isEmpty()) {
            $showCalls = $calls->toArray();
        }

        foreach ($showCalls as $key => $activeCall) {
            $callPage = new Uri();
            $callPage->setOrder($key);
            $callPage->setId($key);
            $callPage->setUri('community/call/index/call-' . $activeCall->getId() . '.html');
            $callPage->setLabel((string)$activeCall);

            /** @var Mvc $page */
            foreach ($pages as $page) {
                if (!$activeCall->hasIdeaTool()
                    && \in_array(
                        $page->getRoute(),
                        ['community/idea/list', 'community/idea/invite/retrieve'],
                        true
                    )
                ) {
                    continue;
                }

                $page->setActive(false);

                $page->setParams(
                    [
                        'toolId' => $activeCall->hasIdeaTool() ? $activeCall->getIdeaTool()->first()->getId() : '',
                        'call'   => $activeCall->getId()
                    ]
                );

                $callPage->addPage(clone $page);
            }
            $this->navigation->addPage($callPage);
        }

        $this->navigation->removePage($callIndex);
    }
}

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

use Laminas\Navigation\Navigation;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\Navigation\Page\Mvc;
use Laminas\Navigation\Page\Uri;
use Laminas\Router\RouteMatch;
use Program\Service\CallService;

use function in_array;

/**
 * Class CallNavigationService
 *
 * @package Program\Navigation\Service
 */
final class CallNavigationService
{
    private AbstractPage $navigation;
    private ?RouteMatch $routeMatch;
    private CallService $callService;

    public function __construct(Navigation $navigation, ?RouteMatch $routeMatch, CallService $callService)
    {
        $this->navigation  = $navigation->current();
        $this->routeMatch  = $routeMatch;
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

        $calls     = $this->callService->findOpenCall();
        $showCalls = [];

        if ($calls->hasUpcoming()) {
            $showCalls[] = $calls->getUpcoming();
        }

        if (! $calls->isEmpty()) {
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
                if (
                    ! $activeCall->hasIdeaTool()
                    && in_array(
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

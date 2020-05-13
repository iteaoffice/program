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
use Laminas\Router\RouteMatch;
use Laminas\Router\RouteStackInterface;
use Program\Service\CallService;
use Project\Entity\Idea\Tool;
use Project\Service\IdeaService;

/**
 * Class CallNavigationService
 *
 * @package Program\Navigation\Service
 */
final class CallNavigationService
{
    private AbstractPage $navigation;
    private ?AbstractPage $communityNavigation;
    private RouteStackInterface $router;
    private ?RouteMatch $routeMatch;
    private CallService $callService;
    private IdeaService $ideaService;

    public function __construct(Navigation $navigation, RouteStackInterface $router, ?RouteMatch $routeMatch, CallService $callService, IdeaService $ideaService)
    {
        $this->navigation          = $navigation->current();
        $this->communityNavigation = $navigation->findOneBy('route', 'community');
        $this->router              = $router;
        $this->routeMatch          = $routeMatch;
        $this->callService         = $callService;
        $this->ideaService         = $ideaService;
    }

    public function __invoke(): void
    {
        if (null === $this->routeMatch) {
            return;
        }

        /** @var Mvc $ideaIndex */
        $callIndex = $this->navigation->findOneBy('id', 'callindex');

        /** @var Mvc $ideaViewPages */
        $ideaViewPages = $this->navigation->findOneBy('route', 'community/idea/view');

        /** @var Mvc $ideaNewPages */
        $ideaNewPages = $this->navigation->findOneBy('route', 'community/idea/new');


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


        //This function needs to check if all toolId's are covered. These toolId might come from the tool index, or from an idea
        //First take the toolId from the routeMatch, this is valid when we view the toolId
        $toBeCoveredToolId = (int)$this->routeMatch->getParam('toolId');


        //When there is an idea, so we have a docRef, this has privilege
        $idea = null;
        if (null !== $this->routeMatch->getParam('docRef')) {
            $idea = $this->ideaService->findIdeaByDocRef($this->routeMatch->getParam('docRef'));
            if (null !== $idea) {
                $toBeCoveredToolId = $idea->getTool()->getId();
            }
        }

        if (null !== $this->routeMatch->getParam('id')) {
            $idea = $this->ideaService->findIdeaById((int)$this->routeMatch->getParam('id'));
            if (null !== $idea) {
                $toBeCoveredToolId = $idea->getTool()->getId();
            }
        }


        //We now collect the toolId's which are covered by the call
        $toolIds = [];
        $key     = 0;
        foreach ($showCalls as $key => $activeCall) {
            $toolId    = $activeCall->hasIdeaTool() ? $activeCall->getIdeaTool()->first()->getId() : '';
            $toolIds[] = $toolId;

            $callPage = new Mvc();
            $callPage->setOrder($key);
            $callPage->setId($key);
            $callPage->setActive($toBeCoveredToolId === $toolId);
            $callPage->setRouter($this->router);
            $callPage->setRoute('community/call/index');
            $callPage->setParams([
                'call' => $activeCall->getId()
            ]);
            $callPage->setLabel((string)$activeCall);

            /** @var Mvc $page */
            foreach ($pages as $page) {
                //Only active the page when we have the same toolId, the notAutoActive will cancel out the auto navigation updater
                $page->setParams(
                    [
                        'toolId' => $toolId,
                        'call'   => $activeCall->getId(),
                    ]
                );

                if ($page->getRoute() === 'community/idea/list') {
                    $page->setActive($toBeCoveredToolId === $toolId);
                    $page->set('notAutoActive', true);
                }

                if ($page->getRoute() === 'community/idea/new') {
                    $page->setActive($toBeCoveredToolId === $toolId);
                    $page->set('notAutoActive', true);
                }

                $callPage->addPage($page);
            }
            $this->navigation->addPage($callPage);
        }

        $this->navigation->removePage($callIndex);

        //Now add a new page, ad hoc for the remaining toolId's
        if (! in_array($toBeCoveredToolId, $toolIds, true)) {
            $tool = $this->ideaService->find(Tool::class, $toBeCoveredToolId);

            if (null !== $tool) {
                $toolPage = new Mvc();
                $toolPage->setOrder($key + 1);
                $toolPage->setId($key);
                $toolPage->setActive(true);
                $toolPage->setRouter($this->router);
                $toolPage->setRoute('community/idea/list');
                $toolPage->setParams(
                    [
                        'toolId' => $tool->getId()
                    ]
                );
                $toolPage->setLabel((string)$tool);
                $toolPage->set('notAutoActive', true);

                $ideaViewPages->setParent($toolPage);
                $ideaNewPages->setActive(true);
                $toolPage->addPage($ideaNewPages);

                $this->navigation->addPage($toolPage);
            }
        }
    }
}

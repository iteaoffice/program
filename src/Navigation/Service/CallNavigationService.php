<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Service;

use Program\Service\CallService;
use Project\Entity\Idea\Tool;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc;
use Zend\Router\RouteMatch;

/**
 * Class CallNavigationService
 *
 * @package Program\Navigation\Service
 */
class CallNavigationService
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
        $this->navigation = $navigation;
        $this->routeMatch = $routeMatch;
        $this->callService = $callService;
    }

    public function __invoke(): void
    {
        if (null === $this->routeMatch) {
            return;
        }

        $activeCall = $this->callService->findLastActiveCall();

        if (null !== $activeCall) {
            //Update the label of the call page
            $callIndex = $this->navigation->findOneBy('id', 'callindex');

            if (null !== $callIndex) {
                $callIndex->setLabel((string)$activeCall);
            }

            /** @var Mvc $ideaIndex */
            $ideaIndex = $this->navigation->findOneBy('id', 'idealist');
            /** @var Tool|bool $tool */
            $tool = $activeCall->getIdeaTool()->first();
            if ($tool && null !== $ideaIndex) {
                $ideaIndex->setParams(['toolId' => $tool->getId()]);
            }

            /** @var Mvc $versiondocumentlist */
            $versiondocumentlist = $this->navigation->findOneBy('id', 'versiondocumentlist');
            if (null !== $versiondocumentlist) {
                $versiondocumentlist->setParams(['call' => $activeCall->getId()]);
            }

            /** @var Mvc $evaluationlist */
            $evaluationlist = $this->navigation->findOneBy('id', 'evaluationlist');
            if (null !== $evaluationlist) {
                $evaluationlist->setParams(['call' => $activeCall->getId()]);
                $evaluationlist->setLabel((string)$callIndex . ' Evaluations');
            }
        }
    }
}

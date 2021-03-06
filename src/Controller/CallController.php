<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Controller;

use Contact\Entity\Contact;
use Event\Service\MeetingService;
use Event\Service\RegistrationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Identity\Identity;
use Laminas\View\Model\ViewModel;
use Program\Service\CallService;
use Project\Service\HelpService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;

/**
 * @method Identity|Contact identity()
 * @method Plugin\GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 */
final class CallController extends AbstractActionController
{
    private CallService $callService;
    private ProjectService $projectService;
    private IdeaService $ideaService;
    private HelpService $helpService;
    private MeetingService $meetingService;
    private RegistrationService $registrationService;

    public function __construct(
        CallService $callService,
        ProjectService $projectService,
        IdeaService $ideaService,
        HelpService $helpService,
        MeetingService $meetingService,
        RegistrationService $registrationService
    ) {
        $this->callService         = $callService;
        $this->projectService      = $projectService;
        $this->ideaService         = $ideaService;
        $this->helpService         = $helpService;
        $this->meetingService      = $meetingService;
        $this->registrationService = $registrationService;
    }


    public function indexAction()
    {
        $callId = $this->params('call');
        $call   = $this->callService->findCallById((int)$callId);

        if (null === $call) {
            return $this->redirect()->toRoute('community');
        }

        $contact = $this->identity();

        $ideas = [];
        $tool  = null;

        $projects = $this->projectService->findInvolvedProjectsByCallAndContact($call, $contact);
        if ($call->hasIdeaTool()) {
            $tool  = $call->getIdeaTool();
            $ideas = $this->ideaService->getInvolvedIdeaAndInviteListByToolAndContact($tool, $contact);
        }

        return new ViewModel(
            [
                'call'                => $call,
                'tool'                => $tool,
                'projects'            => $projects,
                'ideas'               => $ideas,
                'helpTree'            => $this->helpService->getHelpTree(),
                'nextCall'            => $this->callService->findNextCall($call),
                'previousCall'        => $this->callService->findPreviousCall($call),
                'meetingService'      => $this->meetingService,
                'registrationService' => $this->registrationService
            ]
        );
    }
}

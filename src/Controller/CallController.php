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
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller;

use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\IdeaService;
use Project\Service\ProjectService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class SessionManagerController
 *
 * @package Program\Controller
 *
 * @method ZfcUserAuthentication zfcUserAuthentication()
 * @method Plugin\GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 */
final class CallController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    private $programService;
    /**
     * @var CallService
     */
    private $callService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var IdeaService
     */
    private $ideaService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CallController constructor.
     *
     * @param ProgramService      $programService
     * @param CallService         $callService
     * @param ProjectService      $projectService
     * @param IdeaService         $ideaService
     * @param FormService         $formService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProgramService $programService,
        CallService $callService,
        ProjectService $projectService,
        IdeaService $ideaService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->programService = $programService;
        $this->callService = $callService;
        $this->projectService = $projectService;
        $this->ideaService = $ideaService;
        $this->formService = $formService;
        $this->translator = $translator;
    }

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $call = $this->callService->findLastActiveCall();
        $contact = $this->zfcUserAuthentication()->getIdentity();


        $projects = $this->projectService->findProjectsByCallAndContact($call, $contact, ProjectService::WHICH_ALL);
        $ideas = $this->ideaService->findIdeasByCallAndContact($call, $contact);

        return new ViewModel(
            [
                'call'     => $call,
                'projects' => $projects,
                'ideas'    => $ideas,
            ]
        );
    }
}

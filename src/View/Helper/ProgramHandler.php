<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use Content\Entity\Content;
use Content\Entity\Param;
use Program\Entity\Call\Call;
use Program\Entity\Call\Session;
use Program\Entity\Program;
use Program\Options\ModuleOptions;
use Program\Service\CallService;

/**
 * Class ProgramHandler
 *
 * @package Program\View\Helper
 */
class ProgramHandler extends AbstractViewHelper
{
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Call
     */
    protected $call;

    /**
     * @param Content $content
     *
     * @return string
     */
    public function __invoke(Content $content): string
    {
        $this->extractContentParam($content);

        switch ($content->getHandler()->getHandler()) {
            case 'programcall_selector':
                return $this->parseCallSelector($this->getCall());
            case 'programcall_session':
                return $this->parseSessionOverview($this->getSession());
            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param Content $content
     */
    public function extractContentParam(Content $content): void
    {
        /**
         * Go over the handler params and try to see if it is hardcoded or just set via the route
         */
        foreach ($content->getHandler()->getParam() as $parameter) {
            switch ($parameter->getParam()) {
                case 'session':
                    $session = $this->findParamValueFromContent($content, $parameter);

                    if (!\is_null($session)) {
                        $this->setSessionById($session);
                    }
                    break;
                case 'call':
                    $call = $this->findParamValueFromContent($content, $parameter);

                    if (null !== $call) {
                        $this->setCallById($call);
                    }
                    break;
            }
        }
    }

    /**
     * @param Content $content
     * @param Param $param
     *
     * @return null|string
     */
    private function findParamValueFromContent(Content $content, Param $param):?string
    {
        //Hardcoded is always first,If it cannot be found, try to find it from the docref (rule 2)
        foreach ($content->getContentParam() as $contentParam) {
            if ($contentParam->getParameter() === $param && !empty($contentParam->getParameterId())) {
                return $contentParam->getParameterId();
            }
        }

        //Try first to see if the param can be found from the route (rule 1)
        if (!\is_null($this->getRouteMatch()->getParam($param->getParam()))) {
            return $this->getRouteMatch()->getParam($param->getParam());
        }

        //If not found, take rule 3
        return null;
    }


    /**
     * @param $sessionId
     */
    public function setSessionById($sessionId): void
    {
        /** @var Session $session */
        $session = $this->getCallService()->find(Session::class, (int) $sessionId);
        $this->setSession($session);
    }

    /**
     * @return CallService
     */
    public function getCallService(): CallService
    {
        return $this->getServiceManager()->get(CallService::class);
    }

    /**
     * @param $callId
     */
    public function setCallById($callId): void
    {
        $call = $this->getCallService()->findCallById((int) $callId);
        $this->setCall($call);
    }

    /**
     * @param Call $call
     * @param Program $program
     *
     * @return string
     */
    public function parseCallSelector(Call $call = null, Program $program = null): string
    {
        return $this->getRenderer()->render(
            'program/partial/call-selector',
            [
                'displayNameCall' => 'name',
                'calls'           => $this->getCallService()->findNonEmptyAndActiveCalls($program),
                'callId'          => null !== $call ? $call->getId() : null,
            ]
        );
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param Call $call
     *
     * @return ProgramHandler
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function parseSessionOverview(Session $session): string
    {
        return $this->getRenderer()->render('program/partial/entity/session', ['session' => $session]);
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     *
     * @return $this;
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions(): ModuleOptions
    {
        return $this->getServiceManager()->get(ModuleOptions::class);
    }
}

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Program\View\Helper;

use Content\Entity\Handler;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class ProgramHandler
 * @package Program\View\Helper
 */
class ProgramHandler extends AbstractHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var Handler
     */
    protected $handler;

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render()
    {
        switch ($this->getHandler()->getHandler()) {

            case 'programcall_selector':
                return $this->parseCallSelector(
                    !$this->getCallService()->isEmpty() ? $this->getCallService()->getCall() : null,
                    !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null
                );
            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $this->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param Call    $call
     * @param Program $program
     *
     * @return string
     */
    public function parseCallSelector(Call $call = null, Program $program = null)
    {
        return $this->getZfcTwigRenderer()->render(
            'program/partial/call-selector',
            array(
                'calls'             => $this->getCallService()->findAll('Call\Call'),
                'callId'            => !is_null($call) ? $call->getId() : null,
                'selectedProgramId' => !is_null($program) ? $program->getId() : null
            )
        );
    }

    /**
     * @param \Content\Entity\Handler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return \Content\Entity\Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param $callId
     */
    public function setCallId($callId)
    {
        $this->getCallService()->setCallId($callId);
    }

    /**
     * @param $programId
     */
    public function setProgramId($programId)
    {
        $this->getProgramService()->setProgramId($programId);
    }

    /**
     * @return TwigRenderer
     */
    public function getZfcTwigRenderer()
    {
        return $this->serviceLocator->get('ZfcTwigRenderer');
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->serviceLocator->get('application')->getMvcEvent()->getRouteMatch();
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->serviceLocator->get('program_program_service');
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->serviceLocator->get('program_call_service');
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
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}

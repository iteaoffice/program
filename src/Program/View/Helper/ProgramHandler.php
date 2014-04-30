<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
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
 * Create a link to an project
 *
 * @category   Program
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
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
     * @return \Content\Entity\Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param \Content\Entity\Handler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
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
     * @return TwigRenderer
     */
    public function getZfcTwigRenderer()
    {
        return $this->serviceLocator->get('ZfcTwigRenderer');
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->serviceLocator->get('program_call_service');
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->serviceLocator->get('program_program_service');
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
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->serviceLocator->get('application')->getMvcEvent()->getRouteMatch();
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
}

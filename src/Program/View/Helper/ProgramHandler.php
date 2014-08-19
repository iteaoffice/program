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

use Content\Entity\Content;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
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
class ProgramHandler extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;

    /**
     * @param Content $content
     *
     * @return string
     */
    public function __invoke(Content $content)
    {
        $this->extractContentParam($content);
        switch ($content->getHandler()->getHandler()) {
            case 'programcall_selector':
                return $this->parseCallSelector(
                    !$this->getCallService()->isEmpty() ? $this->getCallService()->getCall() : null,
                    !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null
                );
            case 'programcall_info':
                return $this->parseProgramcallInfo(
                    !$this->getCallService()->isEmpty() ? $this->getCallService()->getCall() : null,
                    !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null
                );
            case 'programcall_map':
                return $this->parseProgramcallMap(
                    !$this->getCallService()->isEmpty() ? $this->getCallService()->getCall() : null,
                    !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null
                );
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
    public function extractContentParam(Content $content)
    {
        foreach ($content->getContentParam() as $param) {
            /**
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($param->getParameter()->getParam()) {
                case 'call':
                    if (!is_null($callId = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setCallId($callId);
                    }
                    break;
                case 'program':
                    if (!is_null($programId = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setProgramId($programId);
                    }
                    break;
            }
        }
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
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
     * @param $callId
     */
    public function setCallId($callId)
    {
        $this->getCallService()->setCallId($callId);
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->getServiceLocator()->get(CallService::class);
    }

    /**
     * @param $programId
     */
    public function setProgramId($programId)
    {
        $this->getProgramService()->setProgramId($programId);
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->getServiceLocator()->get(ProgramService::class);
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
                'calls'             => $this->getCallService()->findNonEmptyCalls(),
                'callId'            => !is_null($call) ? $call->getId() : null,
                'selectedProgramId' => !is_null($program) ? $program->getId() : null
            )
        );
    }

    /**
     * @param Call    $call
     * @param Program $program
     *
     * @return string
     */
    public function parseProgramcallInfo(Call $call = null, Program $program = null)
    {
        return $this->getZfcTwigRenderer()->render(
            'program/partial/entity/programcall-info',
            array(
                'calls'             => $this->getCallService()->findNonEmptyCalls(),
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
        return $this->getServiceLocator()->get('ZfcTwigRenderer');
    }
}

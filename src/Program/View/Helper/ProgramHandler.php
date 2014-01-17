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

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Router\Http\RouteMatch;

use Content\Entity\Handler;
use General\View\Helper\CountryMap;
use Program\Entity\Program;
use Program\Entity\Call\Call;
use Program\Service\ProgramService;

/**
 * Class ProgramHandler
 * @package Program\View\Helper
 */
class ProgramHandler extends AbstractHelper
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var Handler
     */
    protected $handler;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;
    /**
     * @var Call
     */
    protected $call;
    /**
     * @var Program
     */
    protected $program;
    /**
     * @var CountryMap
     */
    protected $countryMap;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->programService = $helperPluginManager->getServiceLocator()
            ->get('program_program_service');
        $this->routeMatch     = $helperPluginManager->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $this->countryMap = $helperPluginManager->get('countryMap');
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render()
    {

        $translate = $this->getView()->plugin('translate');

        switch ($this->getHandler()->getHandler()) {

            case 'programcall':
                $this->getView()->headTitle()->append($translate("txt-program-call"));

                return $this->parseCall($this->getCall());
                break;

            case 'programcall_selector':
                return $this->parseCallSelector();

                break;
            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $this->getHandler()->getHandler(),
                    __CLASS__);
        }
    }

    /**
     * Set the projectService based on the id
     *
     * @param $projectId
     */
    public function setProgramId($projectId)
    {
        $this->setProgramService($this->getProgramService()->findEntityById('program', $projectId));
    }

    /**
     * @param \Program\Service\ProgramService $projectService
     */
    public function setProgramService($projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * @return \Program\Service\ProgramService
     */
    public function getProgramService()
    {
        return $this->programService;
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
     * Create a list of calls
     *
     * @return string
     */
    public function parseCallSelector()
    {
        $calls = $this->programService->findAll('Call\Call');

        return $this->getView()->render(
            'program/partial/call-selector',
            array(
                'calls'     => $calls,
                'callId'    => !is_null($this->getCall()) ? $this->getCall()->getId() : null,
                'programId' => !is_null($this->getProgram()) ? $this->getProgram()->getId() : null
            )
        );
    }

    /**
     * @param int $callId
     *
     * @return Call;
     */
    public function setCallId($callId)
    {
        $this->call = $this->programService->findEntityById('Call\Call', $callId);

        return $this->call;
    }

    /**
     * @return \Program\Entity\Call\Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }
}

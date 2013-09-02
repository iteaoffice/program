<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Project\Service\ProjectService;

use Program\Service\ProgramService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\FormService;

/**
 * @category    Program
 * @package     Controller
 */
class ProgramController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function indexAction()
    {
    }

    /**
     * Give a list of programs
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function programsAction()
    {
        $programs = $this->getProgramService()->findAll('program');

        return new ViewModel(array('programs' => $programs));
    }

    /**
     * Show the details of 1 program
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function programAction()
    {
        $program  = $this->getProgramService()->findEntityById(
            'program',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $programs = $this->getProgramService()->findAll('program');

        return new ViewModel(array('program' => $program, 'programs' => $programs));
    }

    /**
     * Show the details of 1 program
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function callsAction()
    {
        $calls = $this->getProgramService()->findCalls();

        return new ViewModel(array('calls' => $calls));
    }

    /**
     * Show the details of 1 call
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function callAction()
    {
        $call = $this->getProgramService()->findEntityById(
            'call',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $projects = $this->getProjectService()->findProjectsPerCall($call);

        return new ViewModel(array('call' => $call, 'projects' => $projects));
    }

    /**
     * @param \Zend\Mvc\Controller\string $layout
     *
     * @return void|\Zend\Mvc\Controller\Plugin\Layout|\Zend\View\Model\ModelInterface
     */
    public function layout($layout)
    {
        if (false === $layout) {
            $this->getEvent()->getViewModel()->setTemplate('layout/nolayout');
        } else {
            $this->getEvent()->getViewModel()->setTemplate('layout/' . $layout);
        }
    }

    /**
     * @return FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return ProgramController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Gateway to the Program Service
     *
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->getServiceLocator()->get('program_generic_service');
    }

    /**
     * @return \Project\Service\ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get('project_project_service');
    }

    /**
     * @param $programService
     *
     * @return ProgramController
     */
    public function setProgramService($programService)
    {
        $this->programService = $programService;

        return $this;
    }
}

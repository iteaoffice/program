<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Project\Service\ProjectService;

use Program\Service\ProgramService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\FormService;
use Program\Options\ModuleOptions;

/**
 * @category    Program
 * @package     Controller
 */
abstract class ProgramAbstractController extends AbstractActionController implements
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
     * @var ModuleOptions
     */
    protected $moduleOptions;

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
        return $this->getServiceLocator()->get('program_program_service');
    }

    /**
     * @return \Project\Service\ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get('project_project_service');
    }

    /**
     * @return \Program\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get('program_module_options');
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

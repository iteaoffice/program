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

use Program\Service\ProgramService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\FormService;
use Program\Entity;

/**
 * @category    Program
 * @package     Controller
 */
class ProgramController extends AbstractActionController implements
    FormServiceAwareInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ProgramService
     */
    protected $programService;
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
        $program = $this->getProgramService()->findEntityById(
            'program',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('program' => $program));
    }

    /**
     * Give a list of facilities
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function facilitiesAction()
    {
        $facilities = $this->getProgramService()->findAll('facility');

        return new ViewModel(array('facilities' => $facilities));
    }

    /**
     * Show the details of 1 facility
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function facilityAction()
    {
        $facility = $this->getProgramService()->findEntityById(
            'facility',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('facility' => $facility));
    }

    /**
     * Give a list of areas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function areasAction()
    {
        $areas = $this->getProgramService()->findAll('area');

        return new ViewModel(array('areas' => $areas));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function areaAction()
    {
        $area = $this->getProgramService()->findEntityById(
            'area',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('area' => $area));
    }

    /**
     * Give a list of area2s
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function area2sAction()
    {
        $area2s = $this->getProgramService()->findAll('area2');

        return new ViewModel(array('area2s' => $area2s));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function area2Action()
    {
        $area2 = $this->getProgramService()->findEntityById(
            'area2',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('area2' => $area2));
    }

    /**
     * Give a list of areas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subAreasAction()
    {
        $subAreas = $this->getProgramService()->findAll('subArea');

        return new ViewModel(array('subAreas' => $subAreas));
    }

    /**
     * Show the details of 1 area
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subAreaAction()
    {
        $subArea = $this->getProgramService()->findEntityById(
            'subArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('subArea' => $subArea));
    }

    /**
     * Give a list of operAreas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operAreasAction()
    {
        $operAreas = $this->getProgramService()->findAll('operArea');

        return new ViewModel(array('operAreas' => $operAreas));
    }

    /**
     * Show the details of 1 operArea
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operAreaAction()
    {
        $operArea = $this->getProgramService()->findEntityById(
            'operArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('operArea' => $operArea));
    }

    /**
     * Give a list of operAreas
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operSubAreasAction()
    {
        $operSubAreas = $this->getProgramService()->findAll('operSubArea');

        return new ViewModel(array('operSubAreas' => $operSubAreas));
    }

    /**
     * Show the details of 1 operArea
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function operSubAreaAction()
    {
        $operSubArea = $this->getProgramService()->findEntityById(
            'operSubArea',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('operSubArea' => $operSubArea));
    }

    /**
     * Edit an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $this->layout(false);
        $entity = $this->getProgramService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-vertical live-form-edit');
        $form->setAttribute('id', 'program-' . strtolower($entity->get('entity_name')) . '-' . $entity->getId());

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->getProgramService()->updateEntity($form->getData());

            $view = new ViewModel(array($this->getEvent()->getRouteMatch()->getParam('entity') => $form->getData()));
            $view->setTemplate(
                "program/partial/" . $this->getEvent()->getRouteMatch()->getParam('entity') . '.twig'
            );

            return $view;
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity));
    }

    /**
     * Trigger to switch layout
     *
     * @param $layout
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

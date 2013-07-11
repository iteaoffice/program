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
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Program\Service\FormServiceAwareInterface;
use Program\Service\ProgramService;
use Program\Service\FormService;

/**
 *
 */
class ProgramManagerController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
{

    /**
     * @var ProgramService;
     */
    protected $programService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

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
     * Give a list of messages
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messagesAction()
    {
        $messages = $this->getProgramService()->findAll('message');

        return new ViewModel(array('messages' => $messages));
    }

    /**
     * Show the details of 1 message
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messageAction()
    {
        $message = $this->getProgramService()->findEntityById(
            'message',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('message' => $message));
    }

    /**
     * Create a new entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $entity = $this->getEvent()->getRouteMatch()->getParam('entity');
        $form   = $this->getFormService()->prepare($this->params('entity'), null, $_POST);

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getProgramService()->newEntity($form->getData());
            $this->redirect()->toRoute(
                'zfcadmin/program-manager/' . strtolower($this->params('entity')),
                array('id' => $result->getId())
            );
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * Edit an entity by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $entity = $this->getProgramService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal live-form');
        $form->setAttribute('id', 'program-program-' . $entity->getId());

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getProgramService()->updateEntity($form->getData());
            $this->redirect()->toRoute(
                'zfcadmin/program/' . strtolower($entity->get('dashed_entity_name')),
                array('id' => $result->getId())
            );
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity, 'fullVersion' => true));
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteAction()
    {
        $entity = $this->getProgramService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $this->getProgramService()->removeEntity($entity);

        return $this->redirect()->toRoute(
            'zfcadmin/program-manager/' . $entity->get('dashed_entity_name') . 's'
        );
    }

    /**
     * @return \Program\Service\FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return ProgramManagerController
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
     * @return ProgramManagerController
     */
    public function setProgramService($programService)
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ProgramManagerController|void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    Controller
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Controller;

use Program\Service\FormService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\ProgramService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Controller
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
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
}

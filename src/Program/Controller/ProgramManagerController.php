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
class ProgramManagerController extends ProgramAbstractController
{

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        /**
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->getEvent()->getRouteMatch()->getParam('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->getEvent()->getRouteMatch()->getParam('entity');
        }

        $entities = $this->getProgramService()->findAll($entityName);

        return new ViewModel(
            ['entities' => $entities, 'entity' => $this->getEvent()->getRouteMatch()->getParam('entity')]
        );
    }

    /**
     * Create a new entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        /**
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->getEvent()->getRouteMatch()->getParam('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->getEvent()->getRouteMatch()->getParam('entity');
        }

        $form = $this->getFormService()->prepare($entityName, null, $_POST);
        $form->setAttribute('class', 'form-horizontal');
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getProgramService()->newEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/program-manager/list',
                [
                    'entity' => strtolower($this->getEvent()->getRouteMatch()->getParam('entity')),
                    'id'     => $result->getId()
                ]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entityName, 'fullVersion' => true]);
    }

    /**
     * Edit an entity by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /**
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->getEvent()->getRouteMatch()->getParam('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->getEvent()->getRouteMatch()->getParam('entity');
        }

        $entity = $this->getProgramService()->findEntityById(
            $entityName,
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal');
        $form->setAttribute('id', 'program-program-' . $entity->getId());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getProgramService()->updateEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/program-manager/list',
                [
                    'entity' => strtolower($entity->get('dashed_entity_name')),
                    'id'     => $result->getId()
                ]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteAction()
    {

        /**
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->getEvent()->getRouteMatch()->getParam('entity') === 'call') {
            $entityName = 'call';
        } else {
            $entityName = $this->getEvent()->getRouteMatch()->getParam('entity');
        }

        $entity = $this->getProgramService()->findEntityById(
            $entityName,
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $this->getProgramService()->removeEntity($entity);

        return $this->redirect()->toRoute(
            'zfcadmin/program-manager/' . $entity->get('dashed_entity_name') . 's'
        );
    }
}

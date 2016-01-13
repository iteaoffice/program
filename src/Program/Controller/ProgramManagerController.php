<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Controller;

use Zend\View\Model\ViewModel;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class ProgramManagerController extends ProgramAbstractController
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        /*
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->params('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->params('entity');
        }

        $entities = $this->getProgramService()->findAll($entityName);

        return new ViewModel(['entities' => $entities, 'entity' => $this->params('entity')]);
    }

    /**
     * Create a new entity.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        /*
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->params('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->params('entity');
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );


        $form = $this->getFormService()->prepare($entityName, null, $data);
        $form->setAttribute('class', 'form-horizontal');
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program-manager/list', [
                    'entity' => $this->params('entity')
                ]);
            }

            if ($form->isValid()) {
                $this->getProgramService()->newEntity($form->getData());

                return $this->redirect()->toRoute('zfcadmin/program-manager/list', [
                    'entity' => strtolower($this->params('entity'))

                ]);
            }
        }

        return new ViewModel(['form' => $form, 'entityName' => $entityName, 'entity' => $this->params('entity')]);
    }

    /**
     * Edit an entity by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /*
         * workaround to find the call\call if that is asked
         * @todo
         */
        if ($this->params('entity') === 'call') {
            $entityName = 'Call\Call';
        } else {
            $entityName = $this->params('entity');
        }

        $entity = $this->getProgramService()->findEntityById($entityName, $this->params('id'));

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );


        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $data);
        $form->setAttribute('class', 'form-horizontal');
        $form->setAttribute('id', 'program-program-' . $entity->getId());
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program-manager/list', [
                    'entity' => $this->params('entity'),
                    'id'     => $this->params('id'),
                ]);
            }

            if ($form->isValid()) {
                $result = $this->getProgramService()->updateEntity($form->getData());

                return $this->redirect()->toRoute('zfcadmin/program-manager/list', [
                    'entity' => strtolower($entity->get('dashed_entity_name'))
                ]);
            } else {
                var_dump($form->getInputFilter()->getMessages());
            }
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }
}

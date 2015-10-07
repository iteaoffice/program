<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Funder
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Program\Controller;

use Program\Entity\Funder;
use Zend\View\Model\ViewModel;

/**
 *
 */
class FunderManagerController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        return new ViewModel(
            [
                'funder' => $this->getProgramService()->findAll('funder'),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        /*
         * @var Funder
         */
        $funder = $this->getProgramService()->findEntityById('funder', $this->params('id'));

        return new ViewModel(
            [
                'funder' => $funder,
            ]
        );
    }

    /**
     * Create a new funder.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare('funder', null, $data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var $funder Funder
             */
            $funder = $this->getProgramService()->newEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/funder-manager/view',
                ['id' => $funder->getId()]
            );
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Edit an funder by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /**
         * @var $funder Funder
         */
        $funder = $this->getProgramService()->findEntityById(
            'funder',
            $this->params('id')
        );

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare($funder->get('entity_name'), $funder, $data);

        $form->get('funder')->get('contact')->setValueOptions([$funder->getContact()->getId() => $funder->getContact()->getDisplayName()]);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (isset($data['delete'])) {
                /**
                 * @var $funder Funder
                 */
                $funder = $form->getData();

                $this->getProgramService()->removeEntity($funder);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf($this->translate("txt-funder-has-successfully-been-deleted"))
                );

                return $this->redirect()->toRoute('zfcadmin/funder-manager/list');
            }

            if (!isset($data['cancel'])) {
                $funder = $this->getProgramService()->updateEntity($funder);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/funder-manager/view',
                ['id' => $funder->getId()]
            );
        }

        return new ViewModel(['form' => $form]);
    }
}

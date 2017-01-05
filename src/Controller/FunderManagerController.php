<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Funder
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Program\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Entity\Funder;
use Program\Form\ProgramFilter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class FunderManagerController
 *
 * @package Program\Controller
 */
class FunderManagerController extends ProgramAbstractController
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->getProgramService()->findEntitiesFiltered(Funder::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ProgramFilter();
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                'encodedFilter' => urlencode($filterPlugin->getHash()),
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
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
        $funder = $this->getProgramService()->findEntityById(Funder::class, $this->params('id'));

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

        $funder = new Funder();
        $form   = $this->getFormService()->prepare($funder, null, $data);

        $form->remove('delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var $funder Funder
             */
            $funder = $form->getData();
            $funder = $this->getProgramService()->newEntity($funder);

            return $this->redirect()->toRoute('zfcadmin/funder/view', ['id' => $funder->getId()]);
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
        $funder = $this->getProgramService()->findEntityById(Funder::class, $this->params('id'));

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare($funder, $funder, $data);

        $form->get($funder->get('underscore_entity_name'))->get('contact')->setValueOptions(
            [
                $funder->getContact()->getId() => $funder->getContact()->getDisplayName(),
            ]
        )->setDisableInArrayValidator(true);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (isset($data['delete'])) {
                /**
                 * @var Funder $funder
                 */
                $funder = $form->getData();

                $this->getProgramService()->removeEntity($funder);
                $this->flashMessenger()->setNamespace('success')
                     ->addMessage(sprintf($this->translate("txt-funder-has-successfully-been-deleted")));

                return $this->redirect()->toRoute('zfcadmin/funder/list');
            }

            if (! isset($data['cancel'])) {
                $funder = $this->getProgramService()->updateEntity($funder);
            }

            return $this->redirect()->toRoute('zfcadmin/funder/view', ['id' => $funder->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }
}

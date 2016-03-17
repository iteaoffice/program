<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Entity\Program;
use Program\Form\ProgramFilter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 */
class ProgramManagerController extends ProgramAbstractController
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->getProgramService()->findEntitiesFiltered(Program::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new ProgramFilter();
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $program = $this->getProgramService()->findEntityById('Program', $this->params('id'));
        if (is_null($program)) {
            return $this->notFoundAction();
        }

        return new ViewModel(['program' => $program]);
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare('Program', null, $data);
        $form->remove('delete');

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /* @var $program Program */
                $program = $form->getData();

                $program = $this->getProgramService()->newEntity($program);
                $this->redirect()->toRoute('zfcadmin/program/view', [
                    'id' => $program->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Edit an template by finding it and program the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /** @var Program $program */
        $program = $this->getProgramService()->findEntityById('Program', $this->params('id'));

        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare($program->get('entity_name'), $program, $data);
        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /** @var Program $program */
                $program = $form->getData();

                /** @var Program $program */
                $program = $this->getProgramService()->updateEntity($program);
                $this->redirect()->toRoute('zfcadmin/program/view', [
                    'id' => $program->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form, 'program' => $program]);
    }
}

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

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Service\GeneralServiceAwareInterface;
use Program\Entity\Call\Call;
use Program\Form\CallFilter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 */
class CallManagerController extends ProgramAbstractController implements GeneralServiceAwareInterface
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->getProgramService()->findEntitiesFiltered(Call::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new CallFilter();
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
        $call = $this->getProgramService()->findEntityById('Call\Call', $this->params('id'));
        if (is_null($call)) {
            return $this->notFoundAction();
        }

        //We need the countries active in the call, to store the funding decisions
        $countries = $this->getGeneralService()->findCountryByCall($call, AffiliationService::WHICH_ALL);

        return new ViewModel([
            'call'      => $call,
            'countries' => $countries
        ]);
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare('Call\Call', null, $data);
        $form->remove('delete');

        $form->setAttribute('class', 'form-horizontal');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/call/list');
            }

            if ($form->isValid()) {
                /* @var $call Call */
                $call = $form->getData();

                $call = $this->getProgramService()->newEntity($call);
                $this->redirect()->toRoute('zfcadmin/call/view', [
                    'id' => $call->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Edit an template by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $call = $this->getProgramService()->findEntityById('Call\Call', $this->params('id'));

        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare($call->get('entity_name'), $call, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/vat/call/list');
            }

            if ($form->isValid()) {
                /** @var Call $call */
                $call = $form->getData();

                /** @var Call $call */
                $call = $this->getCallService()->updateEntity($call);
                $this->redirect()->toRoute('zfcadmin/call/view', [
                    'id' => $call->getId(),
                ]);
            }
        }

        return new ViewModel(['form' => $form, 'call' => $call]);
    }
}

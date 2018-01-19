<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller;

use Program\Entity\Call\Session;
use Program\Form\SessionFilter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class SessionManagerController
 * @package Program\Controller
 *
 * @method Plugin\GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 */
final class SessionManagerController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    private $programService;

    /**
     * @var FormService
     */
    private $formService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SessionManagerController constructor.
     * @param ProgramService      $programService
     * @param FormService         $formService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProgramService      $programService,
        FormService         $formService,
        TranslatorInterface $translator
    )
    {
        $this->programService = $programService;
        $this->formService    = $formService;
        $this->translator     = $translator;
    }

    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->programService->findEntitiesFiltered(Session::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SessionFilter($this->programService->getEntityManager());
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
     * @return ViewModel
     */
    public function viewAction()
    {
        /** @var Session $session */
        $session = $this->programService->findEntityById(Session::class, $this->params('id'));

        return new ViewModel([
            'session' => $session,
        ]);
    }

    /**
     * Create a new funder.
     *
     * @return ViewModel|Response
     */
    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();

        $session = new Session();
        $form = $this->formService->prepare($session, null, $data);

        $form->remove('delete');

        if ($request->isPost() && $form->isValid()) {
            /** @var Session $session */
            $session = $form->getData();
            $this->programService->newEntity($session);

            return $this->redirect()->toRoute('zfcadmin/session/view', ['id' => $session->getId()]);
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * @return ViewModel|Response
     */
    public function editAction()
    {
        /** @var Session $session */
        $session = $this->programService->findEntityById(Session::class, $this->params('id'));

        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($session, $session, $data);

        if ($request->isPost() && $form->isValid()) {
            if (isset($data['delete'])) {
                $this->programService->removeEntity($session);
                $this->flashMessenger()->setNamespace('success')->addMessage(sprintf(
                    $this->translator->translate("txt-session-has-successfully-been-deleted")
                ));

                return $this->redirect()->toRoute('zfcadmin/funder/list');
            }

            if (!isset($data['cancel'])) {
                $this->programService->updateEntity($session);
            }

            return $this->redirect()->toRoute('zfcadmin/funder/view', ['id' => $session->getId()]);
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

}
<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Controller\Plugin\GetFilter;
use Program\Entity\Funder;
use Program\Form\FunderFilter;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

/**
 * @method GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 */
final class FunderManagerController extends AbstractActionController
{
    private ProgramService $programService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        ProgramService $programService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->programService = $programService;
        $this->formService = $formService;
        $this->translator = $translator;
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->programService->findFiltered(Funder::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new FunderFilter();
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

    public function viewAction(): ViewModel
    {
        $funder = $this->programService->find(Funder::class, (int)$this->params('id'));

        if (null === $funder) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'funder' => $funder,
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $funder = new Funder();
        $form = $this->formService->prepare($funder, $data);

        $form->remove('delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /** @var Funder $funder */
            $funder = $form->getData();
            $this->programService->save($funder);

            $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-funder-has-been-created-successfully")));


            return $this->redirect()->toRoute('zfcadmin/funder/view', ['id' => $funder->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        /**
         * @var $funder Funder
         */
        $funder = $this->programService->find(Funder::class, (int)$this->params('id'));

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($funder, $data);

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

                $this->programService->delete($funder);
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-funder-has-been-deleted-successfully")));

                return $this->redirect()->toRoute('zfcadmin/funder/list');
            }

            if (! isset($data['cancel'])) {
                $this->programService->save($funder);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-funder-has-been-updated-successfully")));
            }

            return $this->redirect()->toRoute('zfcadmin/funder/view', ['id' => $funder->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }
}

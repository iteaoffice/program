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
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Program\Controller\Plugin\CallSizeSpreadsheet;
use Program\Controller\Plugin\GetFilter;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Form\ProgramFilter;
use Program\Service\FormService;
use Program\Service\ProgramService;

/**
 * @method FlashMessenger flashMessenger()
 * @method GetFilter getProgramFilter()
 * @method CallSizeSpreadsheet callSizeSpreadsheet(Program $program = null, Call $call = null)
 */
final class ProgramManagerController extends AbstractActionController
{
    private ProgramService $programService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(ProgramService $programService, FormService $formService, TranslatorInterface $translator)
    {
        $this->programService = $programService;
        $this->formService    = $formService;
        $this->translator     = $translator;
    }


    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $query        = $this->programService->findFiltered(Program::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
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

    public function viewAction(): ViewModel
    {
        $program = $this->programService->find(Program::class, (int)$this->params('id'));
        if (null === $program) {
            return $this->notFoundAction();
        }

        return new ViewModel(['program' => $program]);
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $program = new Program();
        $form    = $this->formService->prepare($program, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /* @var $program Program */
                $program = $form->getData();

                $this->programService->save($program);
                return $this->redirect()->toRoute(
                    'zfcadmin/program/view',
                    [
                        'id' => $program->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        /** @var Program $program */
        $program = $this->programService->find(Program::class, (int)$this->params('id'));

        if (null === $program) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($program, $data);

        if (! $this->programService->canDeleteProgram($program)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/view', ['id' => $program->getId()]);
            }

            if (isset($data['delete']) && $this->programService->canDeleteProgram($program)) {
                $this->programService->delete($program);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-program-has-been-deleted-successfully")));

                return $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /** @var Program $program */
                $program = $form->getData();

                $this->programService->save($program);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-program-has-been-updated-successfully")));

                return $this->redirect()->toRoute(
                    'zfcadmin/program/view',
                    [
                        'id' => $program->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'program' => $program]);
    }

    public function exportSizeAction(): Response
    {
        /** @var Program $program */
        $program = $this->programService->find(Program::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $program) {
            return $response;
        }

        return $this->callSizeSpreadsheet($program)->parseResponse();
    }
}

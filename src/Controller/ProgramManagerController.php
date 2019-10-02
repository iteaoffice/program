<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Controller\Plugin\CallSizeSpreadsheet;
use Program\Controller\Plugin\GetFilter;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\Form\ProgramFilter;
use Program\Form\SizeSelect;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class ProgramManagerController
 *
 * @package Program\Controller
 * @method FlashMessenger flashMessenger()
 * @method GetFilter getProgramFilter()
 * @method CallSizeSpreadsheet callSizeSpreadsheet(Program $program = null, Call $call = null)
 */
final class ProgramManagerController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    private $programService;
    /**
     * @var CallService
     */
    private $callService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var VersionService
     */
    private $versionService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ProgramService $programService,
        CallService $callService,
        ProjectService $projectService,
        VersionService $versionService,
        FormService $formService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->programService = $programService;
        $this->callService = $callService;
        $this->projectService = $projectService;
        $this->versionService = $versionService;
        $this->formService = $formService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $query = $this->programService->findFiltered(Program::class, $filterPlugin->getFilter());

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
        $form = $this->formService->prepare($program, $data);
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

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /** @var Program $program */
                $program = $form->getData();

                /** @var Program $program */
                $this->programService->save($program);
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

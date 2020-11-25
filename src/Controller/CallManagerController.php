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

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use General\Service\CountryService;
use General\Service\GeneralService;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Program\Controller\Plugin\CallSizeSpreadsheet;
use Program\Controller\Plugin\CreateCallFundingOverview;
use Program\Controller\Plugin\CreateFundingDownload;
use Program\Controller\Plugin\GetFilter;
use Program\Entity\Call\Call;
use Program\Form\CallFilter;
use Program\Form\FundingFilter;
use Program\Service\CallService;
use Program\Service\FormService;
use Project\Entity\Project;
use Project\Entity\Version\Version;
use Project\Service\ProjectService;
use Project\Service\VersionService;

/**
 * @method FlashMessenger flashMessenger()
 * @method GetFilter getProgramFilter()
 * @method CreateCallFundingOverview createCallFundingOverview($projects, $year)
 * @method CallSizeSpreadsheet callSizeSpreadsheet(array $programs = [], array $call = [])
 * @method CreateFundingDownload createFundingDownload(Call $call)
 */
final class CallManagerController extends AbstractActionController
{
    private CallService $callService;
    private FormService $formService;
    private AffiliationService $affiliationService;
    private ProjectService $projectService;
    private VersionService $versionService;
    private GeneralService $generalService;
    private CountryService $countryService;
    private EntityManager $entityManager;
    private TranslatorInterface $translator;

    public function __construct(
        CallService $callService,
        FormService $formService,
        AffiliationService $affiliationService,
        ProjectService $projectService,
        VersionService $versionService,
        GeneralService $generalService,
        CountryService $countryService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    )
    {
        $this->callService        = $callService;
        $this->formService        = $formService;
        $this->affiliationService = $affiliationService;
        $this->projectService     = $projectService;
        $this->versionService     = $versionService;
        $this->generalService     = $generalService;
        $this->countryService     = $countryService;
        $this->entityManager      = $entityManager;
        $this->translator         = $translator;
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();

        $callQuery = $this->callService->findFiltered(Call::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($callQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new CallFilter();
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
        $call = $this->callService->findCallById((int)$this->params('id'));

        if (null === $call) {
            return $this->notFoundAction();
        }

        // We need the countries active in the call, to store the funding decisions
        $countries = $this->countryService->findCountryByCall($call, AffiliationService::WHICH_ALL);

        return new ViewModel(
            [
                'call'               => $call,
                'countries'          => $countries,
                'generalService'     => $this->generalService,
                'affiliationService' => $this->affiliationService,
            ]
        );
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data    = $request->getPost()->toArray();

        $form = $this->formService->prepare(Call::class, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/call/list');
            }

            if ($form->isValid()) {
                /* @var $call Call */
                $call = $form->getData();
                $this->callService->save($call);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-program-call-has-been-updated-successfully")));

                return $this->redirect()->toRoute('zfcadmin/call/view', ['id' => $call->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var Call $call */
        $call = $this->callService->find(Call::class, (int)$this->params('id'));
        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($call, $data);

        if (!$this->callService->canDeleteCall($call)) {
            $form->remove('delete');
        }

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/call/list');
            }

            if (isset($data['delete']) && $this->callService->canDeleteCall($call)) {

                $this->callService->delete($call);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-program-call-has-been-deleted-successfully")));

                return $this->redirect()->toRoute('zfcadmin/program/list');
            }

            if ($form->isValid()) {
                /** @var Call $call */
                $call = $form->getData();

                $this->callService->save($call);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->translator->translate("txt-program-call-has-been-updated-successfully")));

                return $this->redirect()->toRoute('zfcadmin/call/view', ['id' => $call->getId()]);
            }
        }

        return new ViewModel(['form' => $form, 'call' => $call]);
    }

    public function sizeAction(): ViewModel
    {
        $call = $this->callService->findCallById((int)$this->params('id'));

        if (null === $call) {
            return $this->notFoundAction();
        }

        // Only add the active projects
        $activeProjects = $this->projectService->findProjectsByCall($call, ProjectService::WHICH_ALL);
        $projects       = $activeProjects->getQuery()->getResult();

        // Find the span of the call, because otherwise the matrix will be filled with numbers of year before the call
        $minMaxYearCall  = $this->callService->findMinAndMaxYearInCall($call);
        $yearSpan        = range($minMaxYearCall->minYear, $minMaxYearCall->maxYear);
        $versionOverview = [];

        /** @var Project $project */
        foreach ($projects as $project) {
            /** @var Version $version */
            foreach (array_reverse($this->versionService->findVersionsByProject($project)) as $version) {
                $versionOverview[$project->getId()][$version->getDateSubmitted()->format('Y')][] = [
                    'version' => $version,
                    'cost'    => $this->versionService->findTotalCostVersionByProjectVersion($version),
                ];
            }
        };

        return new ViewModel(
            [
                'call'            => $call,
                'yearSpan'        => $yearSpan,
                'projectService'  => $this->projectService,
                'versionOverview' => $versionOverview,
                'projects'        => $projects,
            ]
        );
    }

    public function exportSizeAction(): Response
    {
        $call = $this->callService->findCallById((int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $call) {
            return $response;
        }

        return $this->callSizeSpreadsheet([], [$call])->parseResponse();
    }

    public function fundingAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $callId  = (int)$this->params('id', $this->callService->findFirstAndLastCall()->lastCall->getId());
        $call    = $this->callService->findCallById($callId);

        if (null === $call) {
            return $this->notFoundAction();
        }

        $minMaxYear = $this->callService->findMinAndMaxYearInCall($call);
        $year       = $this->params('year', $minMaxYear->maxYear);

        /*
         * The form can be used to overrule some parameters. We therefore need to check if the form is set
         * posted correctly and need to update the params when the form has been post
         */
        $form = new FundingFilter($this->entityManager, $minMaxYear);
        $form->setData($request->getPost()->toArray());

        if ($request->isPost() && $form->isValid()) {
            $formData = $form->getData();
            return $this->redirect()->toRoute(
                'zfcadmin/call/funding',
                [
                    'id'   => (int)$formData['call'],
                    'year' => (int)$formData['year'],
                ]
            );
        }

        $form->setData(
            [
                'call' => $callId,
                'year' => $year,
            ]
        );

        $projects = $this->projectService->findProjectsByCall($call)->getQuery()->getResult();

        return new ViewModel(
            [
                'projects'      => $projects,
                'fundingResult' => $this->createCallFundingOverview($projects, $year),
                'year'          => $year,
                'call'          => $call,
                'form'          => $form,
            ]
        );
    }

    public function downloadFundingAction(): Response
    {
        $call = $this->callService->findCallById((int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $call) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $string = $this->createFundingDownload($call);
        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
        // Prepend BOM
        $string = "\xFF\xFE" . $string;


        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"funding_call_$call.csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', \strlen($string));

        $response->setContent($string);

        return $response;
    }
}

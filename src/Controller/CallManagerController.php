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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Controller;

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Entity\Call\Call;
use Program\Form\CallFilter;
use Program\Form\FundingFilter;
use Project\Entity\Project;
use Project\Entity\Version\Version;
use Project\Service\ProjectService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class CallManagerController
 *
 * @package Program\Controller
 */
class CallManagerController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $callQuery = $this->getProgramService()->findEntitiesFiltered(Call::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($callQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
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

    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        $call = $this->getCallService()->findCallById($this->params('id'));

        if (null === $call) {
            return $this->notFoundAction();
        }

        // We need the countries active in the call, to store the funding decisions
        $countries = $this->getGeneralService()->findCountryByCall($call, AffiliationService::WHICH_ALL);

        return new ViewModel(
            [
                'call'           => $call,
                'countries'      => $countries,
                'generalService' => $this->generalService
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();

        $form = $this->getFormService()->prepare(Call::class, null, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                $this->redirect()->toRoute('zfcadmin/call/list');
            }

            if ($form->isValid()) {
                /* @var $call Call */
                $call = $form->getData();
                $call = $this->getProgramService()->newEntity($call);
                return $this->redirect()->toRoute('zfcadmin/call/view', ['id' => $call->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $call = $this->getProgramService()->findEntityById(Call::class, $this->params('id'));
        $data = $request->getPost()->toArray();
        $form = $this->getFormService()->prepare($call, $call, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/vat/call/list');
            }

            if ($form->isValid()) {
                /** @var Call $call */
                $call = $form->getData();

                /** @var Call $call */
                $call = $this->getCallService()->updateEntity($call);
                return $this->redirect()->toRoute('zfcadmin/call/view', ['id' => $call->getId()]);
            }
        }

        return new ViewModel(['form' => $form, 'call' => $call]);
    }

    /**
     * @return ViewModel
     */
    public function sizeAction(): ViewModel
    {
        $call = $this->getCallService()->findCallById($this->params('id'));

        if (null === $call) {
            return $this->notFoundAction();
        }

        // Only add the active projects
        $activeProjects = $this->getProjectService()->findProjectsByCall($call, ProjectService::WHICH_LABELLED);
        $projects = $activeProjects->getQuery()->getResult();

        // Find the span of the call, because otherwise the matrix will be filled with numbers of year before the call
        $minMaxYearCall = $this->getCallService()->findMinAndMaxYearInCall($call);
        $yearSpan = range($minMaxYearCall->minYear, $minMaxYearCall->maxYear);
        $versionOverview = [];

        /** @var Project $project */
        foreach ($projects as $project) {
            /** @var Version $version */
            foreach (array_reverse($this->getVersionService()->findVersionsByProject($project)) as $version) {
                $versionOverview[$project->getId()][$version->getDateSubmitted()->format('Y')][] = [
                    'version' => $version,
                    'cost'    => $this->getVersionService()->findTotalCostVersionByProjectVersion($version),
                ];
            }
        };

        return new ViewModel(
            [
                'call'            => $call,
                'yearSpan'        => $yearSpan,
                'projectService'  => $this->getProjectService(),
                'versionOverview' => $versionOverview,
                'projects'        => $projects,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function fundingAction(): ViewModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $callId = $this->params('id', $this->getCallService()->findFirstAndLastCall()->lastCall->getId());
        $call = $this->getCallService()->findCallById($callId);

        if (null === $call) {
            return $this->notFoundAction();
        }

        $minMaxYear = $this->getCallService()->findMinAndMaxYearInCall($call);

        $year = $this->params('year', $minMaxYear->maxYear);
        /*
         * The form can be used to overrule some parameters. We therefore need to check if the form is set
         * posted correctly and need to update the params when the form has been post
         */
        $form = new FundingFilter($this->getEntityManager(), $minMaxYear);
        $form->setData($request->getPost()->toArray());

        if ($request->isPost() && $form->isValid()) {
            $formData = $form->getData();
            $this->redirect()->toRoute(
                'zfcadmin/call/funding',
                [
                    'id'   => (int)$formData['call'],
                    'year' => (int)$formData['year'],
                ]
            );
        } else {
            $form->setData(
                [
                    'call' => $callId,
                    'year' => $year,
                ]
            );
        }

        $projects = $this->getProjectService()->findProjectsByCall($call)->getQuery()->getResult();

        return new ViewModel(
            [
                'projects'      => $projects,
                'fundingResult' => $this->createCallFundingOverview()->create($projects, $year),
                'year'          => $year,
                'call'          => $call,
                'form'          => $form,
            ]
        );
    }

    /**
     * @return Response|\Zend\Stdlib\ResponseInterface
     */
    public function downloadFundingAction()
    {
        $callId = $this->params('id');
        $call = $this->getCallService()->findCallById($callId);

        if (null === $call) {
            return $this->getResponse();
        }

        $string = $this->createFundingDownload()->create($call);
        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
        // Prepend BOM
        $string = "\xFF\xFE" . $string;

        /** @var Response $response */
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"funding_call_$call.csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }
}

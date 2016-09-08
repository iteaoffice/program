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
use Program\Entity\Call\Call;
use Program\Form\CallFilter;
use Program\Form\FundingFilter;
use Project\Entity\Project;
use Project\Entity\Version\Version;
use Project\Service\ProjectService;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 *
 */
class CallManagerController extends ProgramAbstractController
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->getProgramService()->findEntitiesFiltered(Call::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

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
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $call = $this->getProgramService()->findEntityById(Call::class, $this->params('id'));
        if (is_null($call)) {
            return $this->notFoundAction();
        }

        //We need the countries active in the call, to store the funding decisions
        $countries = $this->getGeneralService()->findCountryByCall($call, AffiliationService::WHICH_ALL);

        return new ViewModel(
            [
                'call'      => $call,
                'countries' => $countries,
            ]
        );
    }

    /**
     * Create a new template.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare(Call::class, null, $data);
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
                $this->redirect()->toRoute(
                    'zfcadmin/call/view',
                    [
                        'id' => $call->getId(),
                    ]
                );
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
        $call = $this->getProgramService()->findEntityById(Call::class, $this->params('id'));
        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare($call, $call, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/program/vat/call/list');
            }

            if ($form->isValid()) {
                /** @var Call $call */
                $call = $form->getData();

                /** @var Call $call */
                $call = $this->getCallService()->updateEntity($call);
                $this->redirect()->toRoute(
                    'zfcadmin/call/view',
                    [
                        'id' => $call->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'call' => $call]);
    }

    /**
     * Edit an template by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function sizeAction()
    {
        $call = $this->getCallService()->findCallById($this->params('id'));

        //Only add the active projects
        $activeProjects = $this->getProjectService()->findProjectsByCall($call, ProjectService::WHICH_LABELLED);
        $projects       = $activeProjects->getQuery()->getResult();

        //Find the span of the call, because otherwise the matrix will be filled with numbers of year before the call
        $minMaxYearCall = $this->getCallService()->findMinAndMaxYearInCall($call);
        $yearSpan       = range($minMaxYearCall->minYear, $minMaxYearCall->maxYear);


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
     * Edit an template by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function fundingAction()
    {
        $callId = $this->params('id', $this->getCallService()->findFirstAndLastCall()->lastCall->getId());


        $call       = $this->getCallService()->findCallById($callId);
        $minMaxYear = $this->getCallService()->findMinAndMaxYearInCall($call);

        $year = $this->params('year', $minMaxYear->maxYear);
        /*
         * The form can be used to overrule some parameters. We therefore need to check if the form is set
         * posted correctly and need to update the params when the form has been post
         */
        $form = new FundingFilter($this->getEntityManager(), $minMaxYear);
        $form->setData($this->getRequest()->getPost()->toArray());

        if ($this->getRequest()->isPost() && $form->isValid()) {
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
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\Stdlib\ResponseInterface
     */
    public function downloadFundingAction()
    {
        $callId = $this->params('id');
        $call   = $this->getCallService()->findCallById($callId);

        $string = $this->createFundingDownload()->create($call);

        //To be able to open the file correctly in Excel, we need to convert it to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF8');

        $response = $this->getResponse();
        $headers  = $response->getHeaders();
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

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
use Program\Form\SizeSelect;
use Project\Entity\Project;
use Project\Service\ProjectService;
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
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->getProgramService()->findEntitiesFiltered(Program::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

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
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $program = $this->getProgramService()->findEntityById(Program::class, $this->params('id'));
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

        $program = new Program();
        $form    = $this->getFormService()->prepare($program, null, $data);
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
                $this->redirect()->toRoute(
                    'zfcadmin/program/view',
                    [
                        'id' => $program->getId(),
                    ]
                );
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
        $program = $this->getProgramService()->findEntityById(Program::class, $this->params('id'));

        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare($program, $program, $data);
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
                $this->redirect()->toRoute(
                    'zfcadmin/program/view',
                    [
                        'id' => $program->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'program' => $program]);
    }

    /**
     * Edit an template by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function sizeAction()
    {
        $filter = $this->getRequest()->getPost()->toArray();

        $form = new SizeSelect($this->getEntityManager(), $this->getCallService());
        $form->setData($filter);

        $program = $this->getProgramService()->findLastProgram();

        if (! is_null($this->params('id'))) {
            $program = $this->getProgramService()->findProgramById($this->params('id'));
        }

        if (isset($filter['filter']['program']) && $this->getRequest()->isPost()) {
            $program = $this->getProgramService()->findProgramById($filter['filter']['program']);
        }

        $form->get('filter')->get('program')->setValue($program->getId());

        $minMaxYear = $this->getProgramService()->findMinAndMaxYearInProgram($program);
        $yearSpan   = range($minMaxYear->minYear, $minMaxYear->maxYear);


        //Go over the projects and add the evaluationTypes in a dedicated matrix
        $projectOverview = [];

        foreach ($program->getCall() as $call) {
            //Only add the active projects
            $activeProjects = $this->getProjectService()->findProjectsByCall($call, ProjectService::WHICH_LABELLED);

            //Find the span of the call, because otherwise the matrix will be filled with numbers of year before the call
            $minMaxYearCall = $this->getCallService()->findMinAndMaxYearInCall($call);
            $yearSpanCall   = range($minMaxYearCall->minYear, $minMaxYearCall->maxYear);


            /** @var Project $project */
            foreach ($activeProjects->getQuery()->getResult() as $project) {
                foreach ($yearSpan as $year) {
                    //Skip the years which are not in the call
                    if (! in_array($year, $yearSpanCall)) {
                        continue;
                    }

                    //Create the last day of the year
                    $dateSubmitted = new \DateTime();

                    //Find the version corresponding to the year
                    $activeVersion = $this->getProjectService()->getLatestProjectVersion(
                        $project,
                        null,
                        $dateSubmitted->modify('last day of december ' . $year)
                    );

                    if (! is_null($activeVersion)) {
                        //We have the version now, add the total cost of that version to the cost of that year
                        $projectOverview[$call->getId()][$year][$project->getDocRef()] = $this->getVersionService()
                            ->findTotalCostVersionByProjectVersion($activeVersion);
                    }
                }
            };
        }

        //Produce an array with the totals
        $totals = [];
        foreach ($projectOverview as $callId => $yearOverview) {
            foreach ($yearOverview as $year => $projects) {
                $totals[$callId][$year] = array_sum($projects);
            }
        }

        return new ViewModel(
            [
                'form'            => $form,
                'program'         => $program,
                'yearSpan'        => $yearSpan,
                'projectService'  => $this->getProjectService(),
                'projectOverview' => $projectOverview,
                'totals'          => $totals,
            ]
        );
    }
}

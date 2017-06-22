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

namespace Program\Controller\Plugin;

use Affiliation\Service\AffiliationService;
use General\Service\GeneralService;
use Program\Entity\Call\Call;
use Project\Entity\Funding\Source;
use Project\Entity\Funding\Status;
use Project\Entity\Project;
use Project\Service\EvaluationService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Special plugin to produce an array with the evaluation.
 *
 * Class CreateEvaluation
 */
class CreateFundingDownload extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface|PluginManager
     */
    protected $serviceLocator;

    /**
     * @param Call $call
     *
     * @return string
     */
    public function create(Call $call): string
    {
        // Open the output stream
        $fh = fopen('php://output', 'w');
        ob_start();

        fputcsv(
            $fh,
            [
                'Program',
                'Call',
                'Project',
                'Project Status',
                'Organisation',
                'Organisation Type',
                'Country',
                'Year',
                'Funding Status',
                'Funding Status code',
                'Cost',
                'General Funding Status',
            ]
        );

        /** @var Project $project */
        foreach ($this->getProjectService()->findProjectsByCall($call)->getQuery()->getResult() as $project) {
            $version = $this->getProjectService()->getLatestProjectVersion($project);

            foreach ($this->getAffiliationService()->findAffiliationByProjectAndWhich($project) as $affiliation) {
                //Find the cost in the year
                //Find the costs of this affiliation (in the given year)
                $costsPerYear = $this->getVersionService()->findTotalCostVersionByAffiliationAndVersionPerYear(
                    $affiliation,
                    $version
                );

                foreach ($affiliation->getFunding() as $funding) {
                    if ($funding->getSource()->getId() === Source::SOURCE_PROJECT_LEADER) {
                        continue;
                    }

                    $year = $funding->getDateStart()->format('Y');

                    if (!is_null($affiliation->getDateSelfFunded())) {
                        $globalStatus = "Self Funded";
                    } else {
                        $globalStatus = null;
                        switch ($funding->getStatus()->getId()) {
                            case Status::STATUS_ALL_GOOD:
                                $globalStatus = 'Funded';
                                break;
                            case Status::STATUS_SELF_FUNDED:
                                $globalStatus = 'Self funded';
                                break;
                            default:
                                $globalStatus = 'Undefined';
                                break;
                        }
                    }


                    fputcsv(
                        $fh,
                        [
                            $project->getCall()->getProgram(),
                            $project->getCall(),
                            $project->parseFullName(),
                            $this->getProjectService()->parseStatus($project),
                            $affiliation->getOrganisation(),
                            $affiliation->getOrganisation()->getType(),
                            $affiliation->getOrganisation()->getCountry()->getIso3(),
                            $year,
                            $funding->getStatus()->getStatusFunding(),
                            $funding->getStatus()->getCode(),
                            (isset($costsPerYear[$year]) ? $costsPerYear[$year] : 0),
                            $globalStatus,

                        ]
                    );
                }
            }
        }

        return ob_get_clean();
    }

    /**
     * Gateway to the Project Service.
     *
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get(ProjectService::class);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface|PluginManager $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Gateway to the Affiliation Service.
     *
     * @return AffiliationService
     */
    public function getAffiliationService()
    {
        return $this->getServiceLocator()->get(AffiliationService::class);
    }

    /**
     * Gateway to the Version Service.
     *
     * @return VersionService
     */
    public function getVersionService()
    {
        return $this->getServiceLocator()->get(VersionService::class);
    }

    /**
     * Gateway to the General Service.
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }

    /**
     * Gateway to the Evaluation Service.
     *
     * @return EvaluationService
     */
    public function getEvaluationService()
    {
        return $this->getServiceLocator()->get(EvaluationService::class);
    }
}

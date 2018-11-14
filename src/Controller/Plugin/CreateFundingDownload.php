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
use Program\Entity\Call\Call;
use Project\Entity\Funding\Source;
use Project\Entity\Funding\Status;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Special plugin to produce an array with the evaluation.
 *
 * Class CreateEvaluation
 */
final class CreateFundingDownload extends AbstractPlugin
{
    /**
     * @var VersionService
     */
    private $versionService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var AffiliationService
     */
    private $affiliationService;

    public function __construct(
        VersionService $versionService,
        ProjectService $projectService,
        AffiliationService $affiliationService
    ) {
        $this->versionService = $versionService;
        $this->projectService = $projectService;
        $this->affiliationService = $affiliationService;
    }

    public function __invoke(Call $call): string
    {
        // Open the output stream
        $fh = \fopen('php://output', 'wb');
        \ob_start();

        \fputcsv(
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
        foreach ($this->projectService->findProjectsByCall($call)->getQuery()->getResult() as $project) {
            $version = $this->projectService->getLatestProjectVersion($project);

            if (null === $version) {
                continue;
            }

            foreach ($this->affiliationService->findAffiliationByProjectAndWhich($project) as $affiliation) {
                //Find the cost in the year
                //Find the costs of this affiliation (in the given year)
                $costsPerYear = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                    $affiliation,
                    $version
                );

                foreach ($affiliation->getFunding() as $funding) {
                    if ($funding->getSource()->getId() === Source::SOURCE_PROJECT_LEADER) {
                        continue;
                    }

                    $year = $funding->getDateStart()->format('Y');

                    if (null !== $affiliation->getDateSelfFunded()) {
                        $globalStatus = 'Self Funded';
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


                    \fputcsv(
                        $fh,
                        [
                            $project->getCall()->getProgram(),
                            $project->getCall(),
                            $project->parseFullName(),
                            $this->projectService->parseStatus($project),
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

        return \ob_get_clean();
    }
}

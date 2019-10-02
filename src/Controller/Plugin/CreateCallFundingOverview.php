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

namespace Program\Controller\Plugin;

use Affiliation\Service\AffiliationService;
use Evaluation\Service\EvaluationService;
use General\Service\CountryService;
use Project\Entity\Funding\Funding;
use Project\Entity\Funding\Source;
use Project\Entity\Funding\Status;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Special plugin to produce an array with the evaluation.
 *
 * Class CreateEvaluation
 */
final class CreateCallFundingOverview extends AbstractPlugin
{
    /**
     * @var CountryService
     */
    private $countryService;
    /**
     * @var VersionService
     */
    private $versionService;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var EvaluationService
     */
    private $evaluationService;
    /**
     * @var AffiliationService
     */
    private $affiliationService;
    /**
     * @var array
     */
    private $countries = [];

    public function __construct(
        CountryService $countryService,
        VersionService $versionService,
        ProjectService $projectService,
        EvaluationService $evaluationService,
        AffiliationService $affiliationService
    ) {
        $this->countryService = $countryService;
        $this->versionService = $versionService;
        $this->projectService = $projectService;
        $this->evaluationService = $evaluationService;
        $this->affiliationService = $affiliationService;
    }

    public function __invoke(array $projects, $year): array
    {
        $evaluationResult = [];

        foreach ($projects as $project) {
            $countries = $this->countryService->findCountryByProject($project, AffiliationService::WHICH_ONLY_ACTIVE);
            foreach ($countries as $country) {
                /*
                 * Create an array of countries to serialize it normally
                 */
                $this->countries[$country->getIso3()] = [
                    'id' => $country->getId(),
                    'country' => $country->getCountry(),
                    'object' => $country,
                    'iso3' => ucwords((string)$country->getIso3()),

                ];


                $value = $this->getValue($project, $country, $year);

                $evalByProjectAndCountry['value'] = $value;
                /*
                 * The evaluation is now an array which contains the evaluation object as first element (with 0 as index)
                 * and partners etc as secondary objects
                 */
                $evaluationResult[$country->getId()][$project->getId()] = $evalByProjectAndCountry;
            }
        }

        \ksort($this->countries);

        $evaluationResult['countries'] = $this->countries;

        return $evaluationResult;
    }

    private function getValue($project, $country, $year): array
    {
        $version = $this->projectService->getLatestApprovedProjectVersion($project);

        if (null === $version) {
            return [];
        }

        //Funding status separation
        /** @var Status $allGood */
        $allGood = $this->projectService->find(Status::class, Status::STATUS_ALL_GOOD);
        /** @var Status $selfFunded */
        $selfFunded = $this->projectService->find(Status::class, Status::STATUS_SELF_FUNDED);
        /** @var Status $default */
        $default = $this->projectService->find(Status::class, Status::STATUS_DEFAULT);

        $costAllGood = 0;
        $costSelfFunded = 0;
        $otherCost = 0;

        //Go over the partners in the project and calculate the cost, group it by funding status
        foreach ($this->affiliationService->findAffiliationByProjectAndCountryAndWhich($project, $country) as $affiliation) {
            //Find the costs of this affiliation (in the given year)
            $costsPerYear = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                $affiliation,
                $version
            );

            /** @var Funding $funding */
            foreach ($affiliation->getFunding() as $funding) {
                if ($funding->getSource()->getId() === Source::SOURCE_OFFICE
                    && $funding->getDateStart()->format('Y') === $year
                ) {
                    $costs = null;
                    if (isset($costsPerYear[$year])) {
                        $costs = $costsPerYear[$year];
                    }

                    if (null !== $affiliation->getDateSelfFunded()) {
                        $costSelfFunded += $costs;
                    } else {
                        //We have now the funding in the given year (office version)
                        switch ($funding->getStatus()->getId()) {
                            case Status::STATUS_ALL_GOOD:
                                $costAllGood += $costs;
                                break;
                            case Status::STATUS_SELF_FUNDED:
                                $costSelfFunded += $costs;
                                break;
                            default:
                                $otherCost += $costs;
                                break;
                        }
                    }
                }
            }
        }


        $value['allGood'] = [
            'status' => $allGood,
            'value'  => $costAllGood,
        ];
        $value['selfFunded'] = [
            'status' => $selfFunded,
            'value'  => $costSelfFunded,
        ];
        $value['other'] = [
            'status' => $default,
            'value'  => $otherCost,
        ];


        return $value;
    }
}

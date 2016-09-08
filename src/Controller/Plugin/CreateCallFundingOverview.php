<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2015 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Controller\Plugin;

use Affiliation\Service\AffiliationService;
use General\Entity\Country;
use General\Service\GeneralService;
use Project\Entity\Evaluation\Evaluation;
use Project\Entity\Evaluation\Type;
use Project\Entity\Funding\Funding;
use Project\Entity\Funding\Source;
use Project\Entity\Funding\Status;
use Project\Entity\Project;
use Project\Entity\Version\Type as VersionType;
use Project\Form\MatrixFilter;
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
class CreateCallFundingOverview extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface|PluginManager
     */
    protected $serviceLocator;
    /**
     * @var array
     */
    protected $countries = [];

    /**
     * @param array $projects
     * @param null  $year
     *
     * @return array
     */
    public function create(array $projects, $year)
    {
        $evaluationResult = [];

        foreach ($projects as $project) {
            $countries = $this->getGeneralService()
                ->findCountryByProject($project, AffiliationService::WHICH_ONLY_ACTIVE);
            foreach ($countries as $country) {
                /*
                 * Create an array of countries to serialize it normally
                 */
                $this->countries[$country->getIso3()] = [
                    'id'      => $country->getId(),
                    'country' => $country->getCountry(),
                    'object'  => $country,
                    'iso3'    => ucwords($country->getIso3()),

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

        ksort($this->countries);

        $evaluationResult['countries'] = $this->countries;

        return $evaluationResult;
    }

    /**
     * @param $project
     * @param $country
     *
     * @return float
     */
    private function getValue($project, $country, $year)
    {
        $version = $this->getProjectService()->getLatestProjectVersion($project);

        //Funding status separation
        /** @var Status $allGood */
        $allGood = $this->getEvaluationService()->findEntityById(Status::class, Status::STATUS_ALL_GOOD);
        /** @var Status $selfFunded */
        $selfFunded = $this->getEvaluationService()->findEntityById(Status::class, Status::STATUS_SELF_FUNDED);
        /** @var Status $default */
        $default = $this->getEvaluationService()->findEntityById(Status::class, Status::STATUS_DEFAULT);

        $costAllGood    = 0;
        $costSelfFunded = 0;
        $otherCost      = 0;

        //Go over the partners in the project and calculate the cost, group it by funding status
        foreach ($this->getAffiliationService()->findAffiliationByProjectAndCountryAndWhich($project, $country) as
            $affiliation) {
            //Find the costs of this affiliation (in the given year)
            $costsPerYear = $this->getVersionService()->findTotalCostVersionByAffiliationAndVersionPerYear(
                $affiliation,
                $version
            );


            foreach ($affiliation->getFunding() as $funding) {
                if ($funding->getSource()->getId() === Source::SOURCE_OFFICE
                    && $funding->getDateStart()->format('Y') == $year
                ) {
                    $costs = null;
                    if (isset($costsPerYear[$year])) {
                        $costs = $costsPerYear[$year];
                    }

                    if (! is_null($affiliation->getDateSelfFunded())) {
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


        $value['allGood']    = [
            'status' => $allGood,
            'value'  => $costAllGood,
        ];
        $value['selfFunded'] = [
            'status' => $selfFunded,
            'value'  => $costSelfFunded,
        ];
        $value['other']      = [
            'status' => $default,
            'value'  => $otherCost,
        ];


        return $value;
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
     * Gateway to the Evaluation Service.
     *
     * @return EvaluationService
     */
    public function getEvaluationService()
    {
        return $this->getServiceLocator()->get(EvaluationService::class);
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
     * Gateway to the Version Service.
     *
     * @return VersionService
     */
    public function getVersionService()
    {
        return $this->getServiceLocator()->get(VersionService::class);
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
}

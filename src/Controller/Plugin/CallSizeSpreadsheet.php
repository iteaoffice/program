<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use DateTime;
use Doctrine\ORM\EntityManager;
use General\Entity\Country;
use General\Service\CountryService;
use Laminas\Http\Headers;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Project\Entity\Funding\Funding;
use Project\Entity\Funding\Source;
use Project\Entity\Project;
use Project\Entity\Version\Type;
use Project\Form\Statistics;
use Project\Service\ContractService;
use Project\Service\ProjectService;
use Project\Service\VersionService;

use function count;
use function end;
use function ini_set;
use function key;
use function ob_end_flush;
use function ob_get_clean;
use function ob_get_length;
use function ob_start;
use function range;
use function set_time_limit;

/**
 * Class SessionSpreadsheet
 *
 * @package Program\Controller\Plugin
 */
final class CallSizeSpreadsheet extends AbstractPlugin
{
    /**
     * @var Program[]
     */
    private array $programs = [];
    /**
     * @var Call[]
     */
    private array $calls = [];
    private array $countryIds = [];
    private array $organisationTypeIds = [];
    /**
     * @var Project[]
     */
    private array $projects = [];
    private array $dateColumns = [];

    private Spreadsheet $spreadsheet;
    private ProjectService $projectService;
    private VersionService $versionService;
    private AffiliationService $affiliationService;
    private ContractService $contractService;
    private ContactService $contactService;
    private CountryService $countryService;
    private EntityManager $entityManager;
    private TranslatorInterface $translator;
    private int $start = 2;

    private bool $includeRejectedPO = false;
    private bool $includeRejectedFPP = false;
    private bool $includeRejectedCR = false;
    private bool $includeDecisionPending = false;
    private bool $includeDeactivatedPartners = false;
    private bool $includePOFPPCostAndEffort = false;
    private bool $includeCancelledProjects = false;
    private bool $splitPerYear = false;
    private bool $includeProjectLeaderData = false;
    private bool $includeTotals = false;

    private array $header = [];
    private array $rows = [];

    public function __construct(
        ProjectService $projectService,
        VersionService $versionService,
        AffiliationService $affiliationService,
        ContractService $contractService,
        ContactService $contactService,
        CountryService $countryService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->projectService     = $projectService;
        $this->versionService     = $versionService;
        $this->affiliationService = $affiliationService;
        $this->contractService    = $contractService;
        $this->contactService     = $contactService;
        $this->countryService     = $countryService;
        $this->entityManager      = $entityManager;
        $this->translator         = $translator;
    }

    public function __invoke(
        array $programs = [],
        array $calls = [],
        array $countryIds = [],
        array $organisationTypeIds = [],
        array $include = [],
        int $output = Statistics::OUTPUT_PROJECTS
    ): self {
        set_time_limit(0);
        ini_set('memory_limit', '2000M');

        $this->spreadsheet = new Spreadsheet();

        if (in_array(Statistics::INCLUDE_REJECTED_PO, $include, false)) {
            $this->includeRejectedPO = true;
        }

        if (in_array(Statistics::INCLUDE_REJECTED_FPP, $include, false)) {
            $this->includeRejectedFPP = true;
        }

        if (in_array(Statistics::INCLUDE_REJECTED_CR, $include, false)) {
            $this->includeRejectedCR = true;
        }

        if (in_array(Statistics::INCLUDE_DECISION_PENDING, $include, false)) {
            $this->includeDecisionPending = true;
        }

        if (in_array(Statistics::INCLUDE_DEACTIVATED_PARTNERS, $include, false)) {
            $this->includeDeactivatedPartners = true;
        }

        if (in_array(Statistics::SPLIT_COSTS_PER_YEAR, $include, false)) {
            $this->splitPerYear = true;
        }

        if (in_array(Statistics::INCLUDE_PO_FPP_COST_AND_EFFORT, $include, false)) {
            $this->includePOFPPCostAndEffort = true;
        }

        if (in_array(Statistics::INCLUDE_TOTALS, $include, false)) {
            $this->includeTotals = true;
        }

        if (in_array(Statistics::INCLUDE_CANCELLED_PROJECTS, $include, false)) {
            $this->includeCancelledProjects = true;
        }

        if (in_array(Statistics::INCLUDE_PROJECT_LEADER_DATA, $include, false)) {
            $this->includeProjectLeaderData = true;
        }

        $this->programs = $programs;
        $this->calls    = $calls;

        $this->parsePrograms($programs);
        $this->parseCalls($calls);

        $this->countryIds          = $countryIds;
        $this->organisationTypeIds = $organisationTypeIds;

        if ($output === Statistics::OUTPUT_PARTNERS) {
            $this->header = $this->getProjectPartnerHeader();
            $this->addProjectPartners($this->projects);
            return $this;
        }

        if ($output === Statistics::OUTPUT_PROJECTS) {
            $this->header = $this->getProjectHeader();
            $this->addProjects($this->projects);
            return $this;
        }


        return $this;
    }

    public function parsePrograms(array $programs): CallSizeSpreadsheet
    {
        if (count($programs) === 0 && count($this->calls) === 0) {
            $programs = $this->entityManager->getRepository(Program::class)->findAll();
        }


        foreach ($programs as $program) {

            /** @var Project $project */
            foreach (
                $this->projectService->findProjectByProgram($program, ProjectService::WHICH_ALL)
                    ->getQuery()
                    ->getResult() as $project
            ) {
                $this->projects[$project->getId()] = $project;
            }
        }

        return $this;
    }

    public function parseCalls(array $calls): CallSizeSpreadsheet
    {
        if (count($calls) === 0 && count($this->programs) === 0) {
            $calls = $this->entityManager->getRepository(Call::class)->findAll();
        }

        foreach ($calls as $call) {

            /** @var Project $project */
            foreach ($this->projectService->findProjectsByCall($call, ProjectService::WHICH_ALL)->getQuery()->getResult() as $project) {
                $this->projects[$project->getId()] = $project;
            }
        }

        return $this;
    }

    private function getProjectPartnerHeader(): array
    {
        $column = 'A';

        $header[$column++] = $this->translator->translate('txt-project-number');
        $header[$column++] = $this->translator->translate('txt-project-name');
        $header[$column++] = $this->translator->translate('txt-program');
        $header[$column++] = $this->translator->translate('txt-program-call');
        $header[$column++] = $this->translator->translate('txt-project-status');
        $header[$column++] = $this->translator->translate('txt-project-partner');
        $header[$column++] = $this->translator->translate('txt-partner-country');
        $header[$column++] = $this->translator->translate('txt-partner-type');
        $header[$column++] = $this->translator->translate('txt-partner-active');
        $header[$column++] = $this->translator->translate('txt-partner-self-funded');

        $header[$column++] = $this->translator->translate('txt-label-date');
        $header[$column++] = $this->translator->translate('txt-official-start-date');
        $header[$column++] = $this->translator->translate('txt-official-end-date');

        if ($this->splitPerYear) {
            $header[$column++] = $this->translator->translate('txt-year');
            $header[$column++] = $this->translator->translate('txt-funding-status');
        }

        if ($this->includePOFPPCostAndEffort) {
            $header[$column++] = $this->translator->translate('txt-effort-po');
            $header[$column++] = $this->translator->translate('txt-cost-po');
            $header[$column++] = $this->translator->translate('txt-effort-fpp');
            $header[$column++] = $this->translator->translate('txt-cost-fpp');
        }

        $header[$column++] = $this->translator->translate('txt-effort-latest-version');
        $header[$column++] = $this->translator->translate('txt-cost-latest-version');
        $header[$column++] = $this->translator->translate('txt-latest-version-type');
        $header[$column++] = $this->translator->translate('txt-latest-version-status');
        $header[$column++] = $this->translator->translate('txt-latest-version-date');

        if ($this->includePOFPPCostAndEffort) {
            $header[$column++] = $this->translator->translate('txt-effort-draft');
            $header[$column++] = $this->translator->translate('txt-cost-draft');
        }

        $header[$column++] = $this->translator->translate('txt-has-contract');
        $header[$column++] = $this->translator->translate('txt-contract-cost-local-currency');
        $header[$column++] = $this->translator->translate('txt-contract-exchange-rate');
        $header[$column++] = $this->translator->translate('txt-contract-cost-euro');
        $header[$column++] = $this->translator->translate('txt-final-cost-euro');

        if ($this->includeTotals) {
            $header[$column++] = $this->translator->translate('txt-total-project-effort');
            $header[$column++] = $this->translator->translate('txt-total-project-cost');
            $header[$column++] = $this->translator->translate('txt-project-countries');
        }

        if ($this->includeProjectLeaderData) {
            $header[$column++] = $this->translator->translate('txt-technical-contact');
            $header[$column++] = $this->translator->translate('txt-technical-contact-email');
            $header[$column++] = $this->translator->translate('txt-vat-number');
            $header[$column++] = $this->translator->translate('txt-address');
            $header[$column++] = $this->translator->translate('txt-zip');
            $header[$column++] = $this->translator->translate('txt-city');
            $header[$column++] = $this->translator->translate('txt-country');
            $header[$column]   = $this->translator->translate('txt-phone');
        }

        return $header;
    }

    private function addProjectPartners(array $projects): void
    {
        /** @var Project $project */
        foreach ($projects as $project) {
            if (! ($this->includeRejectedPO || $this->includeRejectedFPP || $this->includeCancelledProjects)) {
                if (! $this->projectService->isSuccessful($project)) {
                    continue;
                }
            }

            //Find the PO
            $po  = $this->versionService->findVersionTypeById(Type::TYPE_PO);
            $fpp = $this->versionService->findVersionTypeById(Type::TYPE_FPP);

            $projectOutline      = null;
            $fullProjectProposal = null;
            $latestVersion       = null;

            if ($this->includeDecisionPending) {
                $projectOutline      = $this->projectService->getAnyLatestProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getAnyLatestProjectVersion($project, $fpp);
                $latestVersion       = $this->projectService->getAnyLatestProjectVersion($project);

                if (! $this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            } else {
                $projectOutline      = $this->projectService->getLatestReviewedProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getLatestReviewedProjectVersion($project, $fpp);
                $latestVersion       = $this->projectService->getLatestReviewedProjectVersion($project);

                if (! $this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (! $this->includeRejectedPO && null !== $projectOutline && $projectOutline->isReviewed() && $projectOutline->isRejected()) {
                continue;
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (
                ! $this->includeRejectedFPP && null !== $fullProjectProposal && $fullProjectProposal->isReviewed()
                && $fullProjectProposal->isRejected()
            ) {
                continue;
            }

            if (! $this->includeCancelledProjects && $this->projectService->isCancelled($project)) {
                continue;
            }

            //Include cancelled projects destroys a bit the first filter, so remove some leftovers
//            if (
//                $this->includeCancelledProjects
//                && $this->projectService->parseStatus($project) === ProjectService::STATUS_FPP_UNSUBMITTED
//            ) {
//                continue;
//            }


            $affiliations = $this->affiliationService->findAffiliationByProjectAndWhich(
                $project,
                AffiliationService::WHICH_ALL
            );

            /** @var Affiliation $affiliation */
            foreach ($affiliations as $affiliation) {
                if (! $this->includeDeactivatedPartners && ! $affiliation->isActive()) {
                    continue;
                }

                if (
                    ! empty($this->countryIds)
                    && ! in_array(
                        $affiliation->getOrganisation()->getCountry()->getId(),
                        $this->countryIds,
                        false
                    )
                ) {
                    continue;
                }

                if (
                    ! empty($this->organisationTypeIds)
                    && ! in_array(
                        $affiliation->getOrganisation()->getType()->getId(),
                        $this->organisationTypeIds,
                        false
                    )
                ) {
                    continue;
                }

                $latestContractVersion = null;
                if (AffiliationService::useActiveContract($affiliation)) {
                    $latestContractVersion = $this->contractService->findLatestContractVersionByAffiliation(
                        $affiliation
                    );
                }
                $exchangeRate = 1;
                $column       = 'A';


                if ($this->splitPerYear) {
                    $poEffortVersionPerYear     = [];
                    $poCostVersionPerYear       = [];
                    $fppEffortVersionPerYear    = [];
                    $fppCostVersionPerYear      = [];
                    $latestEffortVersionPerYear = [];
                    $latestCostVersionPerYear   = [];
                    $contractCost               = [];

                    if ($this->includePOFPPCostAndEffort) {
                        if (null !== $projectOutline) {
                            $poEffortVersionPerYear
                                = $this->versionService->findTotalEffortVersionByAffiliationAndVersionPerYear(
                                    $affiliation,
                                    $projectOutline
                                );
                            $poCostVersionPerYear
                                = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                                    $affiliation,
                                    $projectOutline
                                );
                        }

                        if (null !== $fullProjectProposal) {
                            $fppEffortVersionPerYear
                                = $this->versionService->findTotalEffortVersionByAffiliationAndVersionPerYear(
                                    $affiliation,
                                    $fullProjectProposal
                                );
                            $fppCostVersionPerYear
                                = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                                    $affiliation,
                                    $fullProjectProposal
                                );
                        }

                        $draftEffort = $this->projectService->findTotalEffortByAffiliationPerYear($affiliation);
                        $draftCost   = $this->projectService->findTotalCostByAffiliationPerYear($affiliation);
                    }

                    if (null !== $latestVersion) {
                        $latestEffortVersionPerYear
                            = $this->versionService->findTotalEffortVersionByAffiliationAndVersionPerYear(
                                $affiliation,
                                $latestVersion
                            );
                        $latestCostVersionPerYear
                            = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                                $affiliation,
                                $latestVersion
                            );
                    }

                    if (null !== $latestContractVersion) {
                        $exchangeRate = $this->contractService->findLatestExchangeRate($latestContractVersion);
                        $contractCost = $this->contractService->findTotalCostVersionByAffiliationAndVersionPerYear(
                            $affiliation,
                            $latestContractVersion
                        );
                    }


                    foreach ($this->projectService->parseYearRange($project, true) as $year) {
                        $projectColumn = [];
                        /** @var Funding $funding */
                        $funding = $affiliation->getFunding()->filter(
                            static function (Funding $funding) use ($year) {
                                return $funding->getSource()->getId() === Source::SOURCE_OFFICE
                                    && $funding->getYear() === (int)$year;
                            }
                        )->first();

                        $fundingStatus = '';
                        if ($funding) {
                            $fundingStatus = $funding->getStatus()->getCode();
                        }

                        if ($affiliation->isSelfFunded()) {
                            $fundingStatus = 'SF';
                        }


                        $projectColumn[$column++] = $project->getNumber();
                        $projectColumn[$column++] = $project->getProject();
                        $projectColumn[$column++] = (string)$project->getCall()->getProgram();
                        $projectColumn[$column++] = (string)$project->getCall();
                        $projectColumn[$column++] = $this->projectService->parseStatus($project);
                        $projectColumn[$column++] = $affiliation->parseBranchedName();
                        $projectColumn[$column++] = $affiliation->getOrganisation()->getCountry()->getIso3();
                        $projectColumn[$column++] = $affiliation->getOrganisation()->getType()->getDescription();
                        $projectColumn[$column++] = $affiliation->isActive() ? 'Y' : 'N';
                        $projectColumn[$column++] = $affiliation->isSelfFunded() ? 'Y' : 'N';

                        if (null !== $fullProjectProposal && $fullProjectProposal->isReviewed() && $fullProjectProposal->isApproved()) {
                            $this->dateColumns[]      = $column;
                            $projectColumn[$column++] = $fullProjectProposal->getDateReviewed();
                        } else {
                            $projectColumn[$column++] = '';
                        }
                        if (null !== $project->getDateStartActual()) {
                            $this->dateColumns[]      = $column;
                            $projectColumn[$column++] = $project->getDateStartActual();
                        } else {
                            $projectColumn[$column++] = '';
                        }
                        if (null !== $project->getDateEndActual()) {
                            $this->dateColumns[]      = $column;
                            $projectColumn[$column++] = $project->getDateEndActual();
                        } else {
                            $projectColumn[$column++] = '';
                        }

                        $projectColumn[$column++] = $year;
                        $projectColumn[$column++] = $fundingStatus;

                        if ($this->includePOFPPCostAndEffort) {
                            $projectColumn[$column++] = $poEffortVersionPerYear[$year] ?? null;
                            $projectColumn[$column++] = $poCostVersionPerYear[$year] ?? null;
                            $projectColumn[$column++] = $fppEffortVersionPerYear[$year] ?? null;
                            $projectColumn[$column++] = $fppCostVersionPerYear[$year] ?? null;
                        }
                        $projectColumn[$column++] = $latestEffortVersionPerYear[$year] ?? null;
                        $projectColumn[$column++] = $latestCostVersionPerYear[$year] ?? null;

                        $projectColumn[$column++] = null !== $latestVersion ? $latestVersion->getVersionType()
                            ->getDescription()
                            : '';
                        $projectColumn[$column++] = null !== $latestVersion ? $this->versionService->parseStatus(
                            $latestVersion
                        )
                            : '';

                        $this->dateColumns[]      = $column;
                        $projectColumn[$column++] = null !== $latestVersion
                        && null !== $latestVersion->getDateReviewed()
                            ? $latestVersion->getDateReviewed() : '';

                        if ($this->includePOFPPCostAndEffort) {
                            $projectColumn[$column++] = $draftEffort[$year] ?? null;
                            $projectColumn[$column++] = $draftCost[$year] ?? null;
                        }
                        $projectColumn[$column++] = null !== $latestContractVersion ? 'Y' : 'N';
                        $projectColumn[$column++] = $contractCost[$year] ?? null;
                        $projectColumn[$column++] = null !== $latestContractVersion ? $exchangeRate : '';
                        $projectColumn[$column++] = ($contractCost[$year] ?? null) / $exchangeRate;
                        $projectColumn[$column++] = null !== $latestContractVersion ? (($contractCost[$year] ?? null)
                            / $exchangeRate)
                            : $latestCostVersionPerYear[$year] ?? null;


                        if ($this->includeTotals) {
                            $totalEffort = 0;
                            $totalCost   = 0;

                            if (null !== $latestVersion) {
                                $totalEffort = $this->versionService->findTotalEffortVersion($latestVersion);
                                $totalCost   = $this->versionService->findTotalCostVersionByProjectVersion($latestVersion);
                            }

                            $projectColumn[$column++] = $totalEffort;
                            $projectColumn[$column++] = $totalCost;

                            //Countries
                            $projectColumn[$column++] = implode(
                                ', ',
                                $this->countryService->findCountryByProject($project)->map(
                                    static function (Country $country) {
                                        return $country->getIso3();
                                    }
                                )->toArray()
                            );
                        }


                        if ($this->includeProjectLeaderData) {
                            $projectColumn[$column++] = $affiliation->getContact()->parseFullName();
                            $projectColumn[$column++] = $affiliation->getContact()->getEmail();

                            //Find the financial
                            $vat = '';
                            if (
                                null !== $affiliation->getFinancial()
                                && null !== $affiliation->getFinancial()->getOrganisation()->getFinancial()
                            ) {
                                $vat = $affiliation->getFinancial()->getOrganisation()->getFinancial()->getVat();
                            }

                            $projectColumn[$column++] = $vat;

                            $mailAddress = null;
                            if (null !== $affiliation->getContact()->getId()) {
                                $mailAddress = $this->contactService->getMailAddress($affiliation->getContact());
                            }
                            $address = null;
                            $zip     = null;
                            $city    = null;
                            $country = null;

                            if (null !== $mailAddress) {
                                $address = $mailAddress->getAddress();
                                $zip     = $mailAddress->getZipCode();
                                $city    = $mailAddress->getCity();
                                $country = $mailAddress->getCountry()->getCountry();
                            }

                            $projectColumn[$column++] = $address;
                            $projectColumn[$column++] = $zip;
                            $projectColumn[$column++] = $city;
                            $projectColumn[$column++] = $country;
                            $projectColumn[$column]   = $this->contactService->getDirectPhone($affiliation->getContact());
                        }

                        $this->rows[] = $projectColumn;
                    }
                }

                if (! $this->splitPerYear) {
                    $projectColumn = [];

                    $poEffortVersion     = null;
                    $poCostVersion       = null;
                    $fppEffortVersion    = null;
                    $fppCostVersion      = null;
                    $latestEffortVersion = null;
                    $latestCostVersion   = null;
                    $contractCost        = null;

                    if (null !== $projectOutline) {
                        $poEffortVersion
                                       = $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                                           $affiliation,
                                           $projectOutline
                                       );
                        $poCostVersion = $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                            $affiliation,
                            $projectOutline
                        );
                    }

                    if (null !== $fullProjectProposal) {
                        $fppEffortVersion
                                        = $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                                            $affiliation,
                                            $fullProjectProposal
                                        );
                        $fppCostVersion = $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                            $affiliation,
                            $fullProjectProposal
                        );
                    }

                    if (null !== $latestVersion) {
                        $latestEffortVersion
                            = $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                                $affiliation,
                                $latestVersion
                            );
                        $latestCostVersion
                            = $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                                $affiliation,
                                $latestVersion
                            );
                    }

                    if (null !== $latestContractVersion) {
                        $exchangeRate = $this->contractService->findLatestExchangeRate($latestContractVersion);
                        $contractCost = array_sum(
                            $this->contractService->findTotalCostVersionByAffiliationAndVersionPerYear(
                                $affiliation,
                                $latestContractVersion
                            )
                        );
                    }

                    $draftEffort = $this->projectService->findTotalEffortByAffiliation($affiliation);
                    $draftCost   = $this->projectService->findTotalCostByAffiliation($affiliation);


                    $projectColumn[$column++] = $project->getNumber();
                    $projectColumn[$column++] = $project->getProject();
                    $projectColumn[$column++] = (string)$project->getCall()->getProgram();
                    $projectColumn[$column++] = (string)$project->getCall();
                    $projectColumn[$column++] = $this->projectService->parseStatus($project);
                    $projectColumn[$column++] = $affiliation->parseBranchedName();
                    $projectColumn[$column++] = $affiliation->getOrganisation()->getCountry()->getIso3();
                    $projectColumn[$column++] = $affiliation->getOrganisation()->getType()->getDescription();
                    $projectColumn[$column++] = $affiliation->isActive() ? 'Y' : 'N';
                    $projectColumn[$column++] = $affiliation->isSelfFunded() ? 'Y' : 'N';

                    if (null !== $fullProjectProposal && $fullProjectProposal->isReviewed() && $fullProjectProposal->isApproved()) {
                        $this->dateColumns[]      = $column;
                        $projectColumn[$column++] = $fullProjectProposal->getDateReviewed();
                    } else {
                        $projectColumn[$column++] = '';
                    }
                    if (null !== $project->getDateStartActual()) {
                        $this->dateColumns[]      = $column;
                        $projectColumn[$column++] = $project->getDateStartActual();
                    } else {
                        $projectColumn[$column++] = '';
                    }
                    if (null !== $project->getDateEndActual()) {
                        $this->dateColumns[]      = $column;
                        $projectColumn[$column++] = $project->getDateEndActual();
                    } else {
                        $projectColumn[$column++] = '';
                    }

                    if ($this->includePOFPPCostAndEffort) {
                        $projectColumn[$column++] = $poEffortVersion;
                        $projectColumn[$column++] = $poCostVersion;
                        $projectColumn[$column++] = $fppEffortVersion;
                        $projectColumn[$column++] = $fppCostVersion;
                    }
                    $projectColumn[$column++] = $latestEffortVersion;
                    $projectColumn[$column++] = $latestCostVersion;

                    $projectColumn[$column++] = null !== $latestVersion ? $latestVersion->getVersionType()
                        ->getDescription()
                        : '';
                    $projectColumn[$column++] = null !== $latestVersion ? $this->versionService->parseStatus(
                        $latestVersion
                    )
                        : '';

                    $this->dateColumns[]      = $column;
                    $projectColumn[$column++] = null !== $latestVersion && null !== $latestVersion->getDateReviewed()
                        ? $latestVersion->getDateReviewed() : '';

                    if ($this->includePOFPPCostAndEffort) {
                        $projectColumn[$column++] = $draftEffort;
                        $projectColumn[$column++] = $draftCost;
                    }

                    $projectColumn[$column++] = null !== $latestContractVersion ? 'Y' : 'N';
                    $projectColumn[$column++] = $contractCost;
                    $projectColumn[$column++] = null !== $latestContractVersion ? $exchangeRate : '';
                    $projectColumn[$column++] = $contractCost / $exchangeRate;
                    $projectColumn[$column++] = null !== $latestContractVersion ? ($contractCost
                        / $exchangeRate)
                        : $latestCostVersion;


                    if ($this->includeTotals) {
                        $totalEffort = 0;
                        $totalCost   = 0;

                        if (null !== $latestVersion) {
                            $totalEffort = $this->versionService->findTotalEffortVersion($latestVersion);
                            $totalCost   = $this->versionService->findTotalCostVersionByProjectVersion($latestVersion);
                        }

                        $projectColumn[$column++] = $totalEffort;
                        $projectColumn[$column++] = $totalCost;

                        //Countries
                        $projectColumn[$column++] = implode(
                            ', ',
                            $this->countryService->findCountryByProject($project)->map(
                                static function (Country $country) {
                                    return $country->getIso3();
                                }
                            )->toArray()
                        );
                    }


                    if ($this->includeProjectLeaderData) {
                        $projectColumn[$column++] = $affiliation->getContact()->parseFullName();
                        $projectColumn[$column++] = $affiliation->getContact()->getEmail();

                        //Find the financial
                        $vat = '';
                        if (
                            null !== $affiliation->getFinancial()
                            && null !== $affiliation->getFinancial()->getOrganisation()->getFinancial()
                        ) {
                            $vat = $affiliation->getFinancial()->getOrganisation()->getFinancial()->getVat();
                        }

                        $projectColumn[$column++] = $vat;

                        $mailAddress = null;
                        if (null !== $affiliation->getContact()->getId()) {
                            $mailAddress = $this->contactService->getMailAddress($affiliation->getContact());
                        }
                        $address = null;
                        $zip     = null;
                        $city    = null;
                        $country = null;

                        if (null !== $mailAddress) {
                            $address = $mailAddress->getAddress();
                            $zip     = $mailAddress->getZipCode();
                            $city    = $mailAddress->getCity();
                            $country = $mailAddress->getCountry()->getCountry();
                        }

                        $projectColumn[$column++] = $address;
                        $projectColumn[$column++] = $zip;
                        $projectColumn[$column++] = $city;
                        $projectColumn[$column++] = $country;
                        $projectColumn[$column]   = $this->contactService->getDirectPhone($affiliation->getContact());
                    }

                    $this->rows[] = $projectColumn;
                }
            }
        }
    }

    private function getProjectHeader(): array
    {
        $column = 'A';

        $header[$column++] = $this->translator->translate('txt-project-number');
        $header[$column++] = $this->translator->translate('txt-project-name');
        $header[$column++] = $this->translator->translate('txt-program');
        $header[$column++] = $this->translator->translate('txt-program-call');
        $header[$column++] = $this->translator->translate('txt-project-status');
        $header[$column++] = $this->translator->translate('txt-project-countries');

        $header[$column++] = $this->translator->translate('txt-label-date');
        $header[$column++] = $this->translator->translate('txt-official-start-date');
        $header[$column++] = $this->translator->translate('txt-official-end-date');

        if ($this->includePOFPPCostAndEffort) {
            $header[$column++] = $this->translator->translate('txt-effort-po');
            $header[$column++] = $this->translator->translate('txt-cost-po');
            $header[$column++] = $this->translator->translate('txt-effort-fpp');
            $header[$column++] = $this->translator->translate('txt-cost-fpp');
        }


        $header[$column++] = $this->translator->translate('txt-total-effort-latest-version');
        $header[$column++] = $this->translator->translate('txt-total-cost-latest-version');
        $header[$column++] = $this->translator->translate('txt-latest-version-type');
        $header[$column++] = $this->translator->translate('txt-latest-version-status');
        $header[$column++] = $this->translator->translate('txt-latest-version-date');

        $header[$column++] = $this->translator->translate('txt-total-project-effort-draft');
        $header[$column++] = $this->translator->translate('txt-total-project-cost-draft');

        if ($this->includeProjectLeaderData) {
            $header[$column++] = $this->translator->translate('txt-project-leader');
            $header[$column++] = $this->translator->translate('txt-project-leader-email');
            $header[$column++] = $this->translator->translate('txt-address');
            $header[$column++] = $this->translator->translate('txt-zip');
            $header[$column++] = $this->translator->translate('txt-city');
            $header[$column++] = $this->translator->translate('txt-country');
            $header[$column]   = $this->translator->translate('txt-phone');
        }

        return $header;
    }

    private function addProjects(array $projects): void
    {
        /** @var Project $project */
        foreach ($projects as $project) {
            if (! ($this->includeRejectedPO || $this->includeRejectedFPP || $this->includeCancelledProjects)) {
                if (! $this->projectService->isSuccessful($project)) {
                    continue;
                }
            }

            //Find the PO
            $po  = $this->versionService->findVersionTypeById(Type::TYPE_PO);
            $fpp = $this->versionService->findVersionTypeById(Type::TYPE_FPP);

            $projectOutline      = null;
            $fullProjectProposal = null;
            $latestVersion       = null;

            if ($this->includeDecisionPending) {
                $projectOutline      = $this->projectService->getAnyLatestProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getAnyLatestProjectVersion($project, $fpp);
                $latestVersion       = $this->projectService->getAnyLatestProjectVersion($project);

                if (! $this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            } else {
                $projectOutline      = $this->projectService->getLatestReviewedProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getLatestReviewedProjectVersion($project, $fpp);
                $latestVersion       = $this->projectService->getLatestReviewedProjectVersion($project);

                if (! $this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (! $this->includeRejectedPO && null !== $projectOutline && $projectOutline->isReviewed() && $projectOutline->isRejected()) {
                continue;
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (
                ! $this->includeRejectedFPP && null !== $fullProjectProposal && $fullProjectProposal->isReviewed()
                && $fullProjectProposal->isRejected()
            ) {
                continue;
            }

            if (! $this->includeCancelledProjects && $this->projectService->isCancelled($project)) {
                continue;
            }

            //Include cancelled projects destroys a bit the first filter, so remove some leftovers
//            if (
//                $this->includeCancelledProjects
//                && $this->projectService->parseStatus($project) === ProjectService::STATUS_FPP_UNSUBMITTED
//            ) {
//                continue;
//            }

            $poEffortVersion     = null;
            $poCostVersion       = null;
            $fppEffortVersion    = null;
            $fppCostVersion      = null;
            $latestEffortVersion = null;
            $latestCostVersion   = null;
            $contractCost        = null;

            if (null !== $projectOutline) {
                $poEffortVersion = $this->versionService->findTotalEffortVersion($projectOutline);
                $poCostVersion   = $this->versionService->findTotalCostVersionByProjectVersion($projectOutline);
            }

            if (null !== $fullProjectProposal) {
                $fppEffortVersion = $this->versionService->findTotalEffortVersion($fullProjectProposal);
                $fppCostVersion   = $this->versionService->findTotalCostVersionByProjectVersion($fullProjectProposal);
            }

            if (null !== $latestVersion) {
                $latestEffortVersion = $this->versionService->findTotalEffortVersion($latestVersion);
                $latestCostVersion   = $this->versionService->findTotalCostVersionByProjectVersion($latestVersion);
            }

            $draftEffort = $this->projectService->findTotalEffortByProject($project);
            $draftCost   = $this->projectService->findTotalCostByProject($project);

            $projectColumn            = [];
            $column                   = 'A';
            $projectColumn[$column++] = $project->getNumber();
            $projectColumn[$column++] = $project->getProject();
            $projectColumn[$column++] = (string)$project->getCall()->getProgram();
            $projectColumn[$column++] = (string)$project->getCall();
            $projectColumn[$column++] = $this->projectService->parseStatus($project);

            //Countries
            $projectColumn[$column++] = implode(
                ', ',
                $this->countryService->findCountryByProject($project)->map(
                    static function (Country $country) {
                        return $country->getIso3();
                    }
                )->toArray()
            );

            if (null !== $fullProjectProposal && $fullProjectProposal->isReviewed() && $fullProjectProposal->isApproved()) {
                $this->dateColumns[]      = $column;
                $projectColumn[$column++] = $fullProjectProposal->getDateReviewed();
            } else {
                $projectColumn[$column++] = '';
            }
            if (null !== $project->getDateStartActual()) {
                $this->dateColumns[]      = $column;
                $projectColumn[$column++] = $project->getDateStartActual();
            } else {
                $projectColumn[$column++] = '';
            }
            if (null !== $project->getDateEndActual()) {
                $this->dateColumns[]      = $column;
                $projectColumn[$column++] = $project->getDateEndActual();
            } else {
                $projectColumn[$column++] = '';
            }

            if ($this->includePOFPPCostAndEffort) {
                $projectColumn[$column++] = $poEffortVersion;
                $projectColumn[$column++] = $poCostVersion;
                $projectColumn[$column++] = $fppEffortVersion;
                $projectColumn[$column++] = $fppCostVersion;
            }
            $projectColumn[$column++] = $latestEffortVersion;
            $projectColumn[$column++] = $latestCostVersion;

            $projectColumn[$column++] = null !== $latestVersion ? $latestVersion->getVersionType()
                ->getDescription()
                : '';
            $projectColumn[$column++] = null !== $latestVersion ? $this->versionService->parseStatus(
                $latestVersion
            )
                : '';
            $projectColumn[$column++] = null !== $latestVersion && null !== $latestVersion->getDateReviewed()
                ? $latestVersion->getDateReviewed() : '';


            $projectColumn[$column++] = $draftEffort;
            $projectColumn[$column++] = $draftCost;

            if ($this->includeProjectLeaderData) {
                $projectColumn[$column++] = $project->getContact()->parseFullName();
                $projectColumn[$column++] = $project->getContact()->getEmail();

                $mailAddress = null;
                if (null !== $project->getContact()->getId()) {
                    $mailAddress = $this->contactService->getMailAddress($project->getContact());
                }
                $address = null;
                $zip     = null;
                $city    = null;
                $country = null;

                if (null !== $mailAddress) {
                    $address = $mailAddress->getAddress();
                    $zip     = $mailAddress->getZipCode();
                    $city    = $mailAddress->getCity();
                    $country = $mailAddress->getCountry()->getCountry();
                }

                $projectColumn[$column++] = $address;
                $projectColumn[$column++] = $zip;
                $projectColumn[$column++] = $city;
                $projectColumn[$column++] = $country;
                $projectColumn[$column]   = $this->contactService->getDirectPhone($project->getContact());
            }


            $this->rows[] = $projectColumn;
        }
    }

    public function parseResponse(): Response
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($this->translator->translate('txt-statistics'));
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(true);
        $sheet->getPageSetup()->setFitToHeight(false);

        $columns = $this->header;
        end($columns);
        $lastColumn = key($columns);
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze the header
        $sheet->freezePane('A2');
        $sheet->fromArray($columns);


        //Add all the rows
        foreach ($this->rows as $row) {
            //Convert the datetimes to Excel dates
            $row = array_map(static function ($element) {
                if ($element instanceof DateTime) {
                    return Date::dateTimeToExcel($element);
                }

                return $element;
            }, $row);

            $sheet->fromArray($row, null, 'A' . $this->start++);
        }

        foreach ($this->dateColumns as $dateColumn) {
            $sheet->getStyle($dateColumn . '2:' . $dateColumn . '6000')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        }

        $response = new Response();

        if (! ($this->spreadsheet instanceof Spreadsheet)) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var Xlsx $writer */
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        ob_start();
        $gzip = false;
        // Gzip the output when possible. @see http://php.net/manual/en/function.ob-gzhandler.php
        if (ob_start('ob_gzhandler')) {
            $gzip = true;
        }
        $writer->save('php://output');
        if ($gzip) {
            ob_end_flush(); // Flush the gzipped buffer into the main buffer
        }
        $contentLength = ob_get_length();

        // Prepare the response
        $response->setContent(ob_get_clean());
        $response->setStatusCode(Response::STATUS_CODE_200);
        $header = new Headers();
        $header->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="' . $this->parseFileName() . '.xlsx"',
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Length'      => $contentLength,
                'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        if ($gzip) {
            $header->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($header);

        return $response;
    }

    public function parseFileName(): string
    {
        return 'Statistics';
    }

    public function parseResult(): array
    {
        return [
            'header' => $this->header,
            'rows'   => $this->rows
        ];
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}

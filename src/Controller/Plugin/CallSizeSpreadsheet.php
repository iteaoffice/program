<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
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
    private $programs = [];
    /**
     * @var Call[]
     */
    private $calls = [];
    /**
     * @var array
     */
    private $countryIds = [];
    /**
     * @var array
     */
    private $organisationTypeIds = [];

    /**
     * @var Project[]
     */
    private $projects = [];


    /**
     * @var Spreadsheet
     */
    private $spreadsheet;
    /**
     * @var ProjectService
     */
    private $projectService;
    /**
     * @var VersionService
     */
    private $versionService;
    /**
     * @var AffiliationService
     */
    private $affiliationService;
    /**
     * @var ContractService
     */
    private $contractService;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    private $start = 2;

    private $includeRejectedPO = false;
    private $includeRejectedFPP = false;
    private $includeRejectedCR = false;
    private $includeDecisionPending = false;
    private $includeDeactivatedPartners = false;
    private $splitPerYear = false;

    public function __construct(
        ProjectService $projectService,
        VersionService $versionService,
        AffiliationService $affiliationService,
        ContractService $contractService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->projectService = $projectService;
        $this->versionService = $versionService;
        $this->affiliationService = $affiliationService;
        $this->contractService = $contractService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function __invoke(
        array $programs = [],
        array $calls = [],
        array $countryIds = [],
        array $organisationTypeIds = [],
        array $include = []
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


        if (null !== $programs) {
            $this->parsePrograms($programs);
        }

        if (null !== $calls) {
            $this->parseCalls($calls);
        }

        $this->countryIds = $countryIds;
        $this->organisationTypeIds = $organisationTypeIds;

        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($this->translator->translate('txt-statistics'));
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(true);
        $sheet->getPageSetup()->setFitToHeight(false);

        // Header
        $columns = $this->getHeaders();
        end($columns);

        $lastColumn = key($columns);
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze the header
        $sheet->freezePane('A2');
        $sheet->fromArray($columns);

        $this->addProjectsToSheet($sheet, $this->projects);

        return $this;
    }

    public function parsePrograms(array $programs): CallSizeSpreadsheet
    {
        $this->programs = $programs;

        foreach ($programs as $program) {
            foreach ($this->projectService->findProjectByProgram($program, ProjectService::WHICH_ALL)
                    ->getQuery()
                    ->getResult() as $project) {
                $this->projects[] = $project;
            }
        }

        return $this;
    }

    public function parseCalls(array $calls): CallSizeSpreadsheet
    {
        $this->calls = $calls;

        foreach ($calls as $call) {
            foreach ($this->projectService->findProjectsByCall($call, ProjectService::WHICH_ALL)->getQuery()->getResult() as $project) {
                $this->projects[] = $project;
            }
        }

        return $this;
    }

    private function getHeaders(): array
    {
        $column = 'A';
        return [
            $column++ => $this->translator->translate('txt-project-number'),
            $column++ => $this->translator->translate('txt-project-name'),
            $column++ => $this->translator->translate('txt-program'),
            $column++ => $this->translator->translate('txt-program-call'),
            $column++ => $this->translator->translate('txt-project-status'),
            $column++ => $this->translator->translate('txt-project-partner'),
            $column++ => $this->translator->translate('txt-partner-country'),
            $column++ => $this->translator->translate('txt-partner-type'),
            $column++ => $this->translator->translate('txt-partner-active'),
            $column++ => $this->translator->translate('txt-year'),
            $column++ => $this->translator->translate('txt-funding-status'),
            $column++ => $this->translator->translate('txt-effort-po'),
            $column++ => $this->translator->translate('txt-cost-po'),
            $column++ => $this->translator->translate('txt-effort-fpp'),
            $column++ => $this->translator->translate('txt-cost-fpp'),
            $column++ => $this->translator->translate('txt-effort-latest-version'),
            $column++ => $this->translator->translate('txt-cost-latest-version'),
            $column++ => $this->translator->translate('txt-latest-version-type'),
            $column++ => $this->translator->translate('txt-latest-version-status'),
            $column++ => $this->translator->translate('txt-latest-version-date'),
            $column++ => $this->translator->translate('txt-effort-draft'),
            $column++ => $this->translator->translate('txt-cost-draft'),
            $column++ => $this->translator->translate('txt-has-contract'),
            $column++ => $this->translator->translate('txt-contract-cost-local-currency'),
            $column++ => $this->translator->translate('txt-contract-exchange-rate'),
            $column++ => $this->translator->translate('txt-contract-cost-euro'),
            $column   => $this->translator->translate('txt-final-cost-euro'),
        ];
    }

    private function addProjectsToSheet(Worksheet $sheet, array $projects): void
    {
        /** @var Project $project */
        foreach ($projects as $project) {
            if (!($this->includeRejectedPO || $this->includeRejectedFPP)
                && !$this->projectService->isSuccessful(
                    $project
                )
            ) {
                continue;
            }

            //Find the PO
            $po = $this->versionService->findVersionTypeById(Type::TYPE_PO);
            $fpp = $this->versionService->findVersionTypeById(Type::TYPE_FPP);

            //private $includeRejectedPO = false;
            //private $includeRejectedFPP = false;
            //private $includeRejectedCR = false;
            //private $includeDecisionPending = false;

            $projectOutline = null;
            $fullProjectProposal = null;
            $latestVersion = null;

            if ($this->includeDecisionPending) {
                $projectOutline = $this->projectService->getAnyLatestProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getAnyLatestProjectVersion($project, $fpp);
                $latestVersion = $this->projectService->getAnyLatestProjectVersion($project);

                if (!$this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            } else {
                $projectOutline = $this->projectService->getLatestReviewedProjectVersion($project, $po);
                $fullProjectProposal = $this->projectService->getLatestReviewedProjectVersion($project, $fpp);
                $latestVersion = $this->projectService->getLatestReviewedProjectVersion($project);

                if (!$this->includeRejectedCR && null !== $latestVersion && $latestVersion->isRejected()) {
                    $latestVersion = $this->projectService->getLatestApprovedProjectVersion($project);
                }
            }

            //Don't do anything when no project outline has been submitted
            if (null === $projectOutline) {
                continue;
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (!$this->includeRejectedPO && $projectOutline->isReviewed() && $projectOutline->isRejected()) {
                continue;
            }

            //Stop the process when we don't include rejected PO and when the PO has not been approved
            if (!$this->includeRejectedFPP && null !== $fullProjectProposal && $fullProjectProposal->isReviewed()
                && $fullProjectProposal->isRejected()
            ) {
                continue;
            }


            $affiliations = $this->affiliationService->findAffiliationByProjectAndWhich(
                $project,
                AffiliationService::WHICH_ALL
            );

            /** @var Affiliation $affiliation */
            foreach ($affiliations as $affiliation) {
                if (!$this->includeDeactivatedPartners && !$affiliation->isActive()) {
                    continue;
                }

                if (!empty($this->countryIds)
                    && !in_array(
                        $affiliation->getOrganisation()->getCountry()->getId(),
                        $this->countryIds,
                        false
                    )
                ) {
                    continue;
                }

                if (!empty($this->organisationTypeIds)
                    && !in_array(
                        $affiliation->getOrganisation()->getType()->getId(),
                        $this->organisationTypeIds,
                        false
                    )
                ) {
                    continue;
                }

                $latestContractVersion = $this->contractService->findLatestContractVersionByAffiliation($affiliation);
                $exchangeRate = 1;







                $column = 'A';


                if ($this->splitPerYear) {
                    $poEffortVersionPerYear = [];
                    $poCostVersionPerYear = [];
                    $fppEffortVersionPerYear = [];
                    $fppCostVersionPerYear = [];
                    $latestEffortVersionPerYear = [];
                    $latestCostVersionPerYear = [];
                    $contractCost = [];

                    if (null !== $projectOutline) {
                        $poEffortVersionPerYear
                            = $this->versionService->findTotalEffortVersionByAffiliationAndVersionPerYear(
                                $affiliation,
                                $projectOutline
                            );
                        $poCostVersionPerYear = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
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
                        $fppCostVersionPerYear = $this->versionService->findTotalCostVersionByAffiliationAndVersionPerYear(
                            $affiliation,
                            $fullProjectProposal
                        );
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

                    $draftEffort = $this->projectService->findTotalEffortByAffiliationPerYear($affiliation);
                    $draftCost = $this->projectService->findTotalCostByAffiliationPerYear($affiliation);

                    foreach ($this->projectService->parseYearRange($project, true) as $year) {

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


                        $projectColumn = [
                            $column++ => $project->getNumber(),
                            $column++ => $project->getProject(),
                            $column++ => (string)$project->getCall()->getProgram(),
                            $column++ => (string)$project->getCall(),
                            $column++ => $this->projectService->parseStatus($project),
                            $column++ => $affiliation->parseBranchedName(),
                            $column++ => $affiliation->getOrganisation()->getCountry()->getIso3(),
                            $column++ => $affiliation->getOrganisation()->getType()->getDescription(),
                            $column++ => $affiliation->isActive() ? 'Y' : 'N',
                            $column++ => $year,
                            $column++ => $fundingStatus,
                            $column++ => $poEffortVersionPerYear[$year] ?? null,
                            $column++ => $poCostVersionPerYear[$year] ?? null,
                            $column++ => $fppEffortVersionPerYear[$year] ?? null,
                            $column++ => $fppCostVersionPerYear[$year] ?? null,
                            $column++ => $latestEffortVersionPerYear[$year] ?? null,
                            $column++ => $latestCostVersionPerYear[$year] ?? null,

                            $column++ => null !== $latestVersion ? $latestVersion->getVersionType()->getDescription()
                                : '',
                            $column++ => null !== $latestVersion ? $this->versionService->parseStatus($latestVersion)
                                : '',
                            $column++ => null !== $latestVersion && null !== $latestVersion->getDateReviewed()
                                ? $latestVersion->getDateReviewed()->format('d-m-Y') : '',

                            $column++ => $draftEffort[$year] ?? null,
                            $column++ => $draftCost[$year] ?? null,
                            $column++ => null !== $latestContractVersion ? 'Y' : 'N',
                            $column++ => $contractCost[$year] ?? null,
                            $column++ => null !== $latestContractVersion ? $exchangeRate : '',
                            $column++ => ($contractCost[$year] ?? null) / $exchangeRate,
                            $column   => null !== $latestContractVersion ? (($contractCost[$year] ?? null)
                                / $exchangeRate)
                                : $latestCostVersionPerYear[$year] ?? null
                        ];

                        $sheet->fromArray($projectColumn, null, 'A' . $this->start++);
                    }
                }

                if (!$this->splitPerYear) {
                    $poEffortVersion = null;
                    $poCostVersion = null;
                    $fppEffortVersion = null;
                    $fppCostVersion = null;
                    $latestEffortVersion = null;
                    $latestCostVersion = null;
                    $contractCost = null;

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
                        $contractCost = array_sum($this->contractService->findTotalCostVersionByAffiliationAndVersionPerYear(
                            $affiliation,
                            $latestContractVersion
                        ));
                    }

                    $draftEffort = $this->projectService->findTotalEffortByAffiliation($affiliation);
                    $draftCost = $this->projectService->findTotalCostByAffiliation($affiliation);





                    $projectColumn = [
                            $column++ => $project->getNumber(),
                            $column++ => $project->getProject(),
                            $column++ => (string)$project->getCall()->getProgram(),
                            $column++ => (string)$project->getCall(),
                            $column++ => $this->projectService->parseStatus($project),
                            $column++ => $affiliation->parseBranchedName(),
                            $column++ => $affiliation->getOrganisation()->getCountry()->getIso3(),
                            $column++ => $affiliation->getOrganisation()->getType()->getDescription(),
                            $column++ => $affiliation->isActive() ? 'Y' : 'N',
                            $column++ => null,
                            $column++ => null,
                            $column++ => $poEffortVersion,
                            $column++ => $poCostVersion,
                            $column++ => $fppEffortVersion,
                            $column++ => $fppCostVersion,
                            $column++ => $latestEffortVersion,
                            $column++ => $latestCostVersion,

                            $column++ => null !== $latestVersion ? $latestVersion->getVersionType()->getDescription()
                                : '',
                            $column++ => null !== $latestVersion ? $this->versionService->parseStatus($latestVersion)
                                : '',
                            $column++ => null !== $latestVersion && null !== $latestVersion->getDateReviewed()
                                ? $latestVersion->getDateReviewed()->format('d-m-Y') : '',

                            $column++ => $draftEffort,
                            $column++ => $draftCost,
                            $column++ => null !== $latestContractVersion ? 'Y' : 'N',
                            $column++ => $contractCost,
                            $column++ => null !== $latestContractVersion ? $exchangeRate : '',
                            $column++ => $contractCost / $exchangeRate,
                            $column   => null !== $latestContractVersion ? ($contractCost
                                / $exchangeRate)
                                : $latestCostVersion
                        ];

                    $sheet->fromArray($projectColumn, null, 'A' . $this->start++);
                }

                $this->entityManager->clear();
            }
        }
    }

    public function parseResponse(): Response
    {
        $response = new Response();

        if (!($this->spreadsheet instanceof Spreadsheet)) {
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
        $headers = new Headers();
        $headers->addHeaders(
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
            $headers->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($headers);

        return $response;
    }

    public function parseFileName(): string
    {
        return 'Statistics';
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}

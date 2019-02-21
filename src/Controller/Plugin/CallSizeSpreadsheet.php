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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Program\Entity\Call\Call;
use Project\Entity\Project;
use Project\Entity\Version\Type;
use Project\Service\ContractService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class SessionSpreadsheet
 *
 * @package Program\Controller\Plugin
 */
final class CallSizeSpreadsheet extends AbstractPlugin
{
    /**
     * @var Call
     */
    private $call;
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
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ProjectService $projectService, VersionService $versionService,
        AffiliationService $affiliationService, ContractService $contractService, TranslatorInterface $translator
    ) {
        $this->projectService = $projectService;
        $this->versionService = $versionService;
        $this->affiliationService = $affiliationService;
        $this->contractService = $contractService;
        $this->translator = $translator;
    }


    public function __invoke(Call $call): CallSizeSpreadsheet
    {
        $this->call = $call;
        $this->spreadsheet = new Spreadsheet();
        //$this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        //Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);

        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->setTitle($this->translator->translate((string) $call));
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(true);
        $sheet->getPageSetup()->setFitToHeight(false);

        // Header
        $columns = [
            'A' => $this->translator->translate('txt-project'),
            'B' => $this->translator->translate('txt-project-status'),
            'C' => $this->translator->translate('txt-project-partner'),
            'D' => $this->translator->translate('txt-partner-country'),
            'E' => $this->translator->translate('txt-partner-type'),
            'F' => $this->translator->translate('txt-partner-active'),
            'G' => $this->translator->translate('txt-effort-po'),
            'H' => $this->translator->translate('txt-cost-po'),
            'I' => $this->translator->translate('txt-effort-fpp'),
            'J' => $this->translator->translate('txt-cost-fpp'),
            'K' => $this->translator->translate('txt-effort-latest-version'),
            'L' => $this->translator->translate('txt-cost-latest-version'),
            'M' => $this->translator->translate('txt-effort-draft'),
            'N' => $this->translator->translate('txt-cost-draft'),
            'O' => $this->translator->translate('txt-contract-cost-local-currency'),
            'P' => $this->translator->translate('txt-contract-exchange-rate'),
            'Q' => $this->translator->translate('txt-contract-cost-euro'),
        ];
        \end($columns);

        $lastColumn = \key($columns);
        foreach (\range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze the header
        $sheet->freezePane('A2');
        $sheet->fromArray($columns);

        $start = 2;

        /** @var Project $project */
        foreach (
            $this->projectService->findProjectsByCall($call, ProjectService::WHICH_ALL)->getQuery()->getResult() as
            $project
        ) {
            //Find the PO
            $po = $this->versionService->findVersionTypeById(Type::TYPE_PO);
            $fpp = $this->versionService->findVersionTypeById(Type::TYPE_FPP);
            $projectOutline = $this->projectService->getLatestProjectVersion($project, $po);
            $fullProjectProposal = $this->projectService->getLatestProjectVersion($project, $fpp);
            $latestVersion = $this->projectService->getLatestProjectVersion($project);

            /** @var Affiliation $affiliation */
            foreach (
                $this->affiliationService->findAffiliationByProjectAndWhich(
                    $project, AffiliationService::WHICH_ALL
                ) as $affiliation
            ) {
                $latestContractVersion = $this->contractService->findLatestContractVersionByAffiliation($affiliation);
                $exchangeRate = 1;
                $contractCost = '';
                if (null !== $latestContractVersion) {
                    $exchangeRate = $this->contractService->findLatestExchangeRate($latestContractVersion);
                    $contractCost = $this->contractService->findTotalCostByAffiliationInVersion(
                        $latestContractVersion, $affiliation
                    );
                }

                $column = [
                    'A' => $project->parseFullName(),
                    'B' => $this->projectService->parseStatus($project),
                    'C' => $affiliation->parseBranchedName(),
                    'D' => $affiliation->getOrganisation()->getCountry()->getIso3(),
                    'E' => $affiliation->getOrganisation()->getType()->getDescription(),
                    'F' => $affiliation->isActive() ? 'Y' : 'N',
                    'G' => null !== $projectOutline
                        ? $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                            $affiliation, $projectOutline
                        ) : null,
                    'H' => null !== $projectOutline
                        ? $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                            $affiliation, $projectOutline
                        ) : null,
                    'I' => null !== $fullProjectProposal
                        ? $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                            $affiliation, $fullProjectProposal
                        ) : null,
                    'J' => null !== $fullProjectProposal
                        ? $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                            $affiliation, $fullProjectProposal
                        ) : null,
                    'K' => null !== $latestVersion
                        ? $this->versionService->findTotalEffortVersionByAffiliationAndVersion(
                            $affiliation, $latestVersion
                        ) : null,
                    'L' => null !== $latestVersion ? $this->versionService->findTotalCostVersionByAffiliationAndVersion(
                        $affiliation, $latestVersion
                    ) : null,
                    'M' => $this->projectService->findTotalEffortByAffiliation($affiliation),
                    'N' => $this->projectService->findTotalCostByAffiliation($affiliation),
                    'O' => null !== $latestContractVersion ? $contractCost : '',
                    'P' => null !== $latestContractVersion ? $exchangeRate : '',
                    'Q' => null !== $latestContractVersion ? $contractCost / $exchangeRate : '',
                ];


                $sheet->fromArray($column, null, 'A' . $start++);

            }
        }

        return $this;
    }

    public function parseResponse(): Response
    {
        $response = new Response();
        if (!($this->spreadsheet instanceof Spreadsheet)) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var Xlsx $writer */
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        \ob_start();
        $gzip = false;
        // Gzip the output when possible. @see http://php.net/manual/en/function.ob-gzhandler.php
        if (\ob_start('ob_gzhandler')) {
            $gzip = true;
        }
        $writer->save('php://output');
        if ($gzip) {
            \ob_end_flush(); // Flush the gzipped buffer into the main buffer
        }
        $contentLength = \ob_get_length();

        // Prepare the response
        $response->setContent(\ob_get_clean());
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="' . $this->call->__toString() . '.xlsx"',
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

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}

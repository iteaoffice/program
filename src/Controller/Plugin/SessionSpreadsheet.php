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

use Application\Service\AssertionService;
use BjyAuthorize\Service\Authorize;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Program\Entity\Call\Session;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use Zend\View\HelperPluginManager;

/**
 * Class SessionSpreadsheet
 *
 * @package Program\Controller\Plugin
 */
final class SessionSpreadsheet extends AbstractPlugin
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;
    /**
     * @var AssertionService
     */
    private $assertionService;
    /**
     * @var Authorize
     */
    private $authorize;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var Url
     */
    private $urlHelper;
    /**
     * @var ServerUrl
     */
    private $serverUrlHelper;

    public function __construct(
        AssertionService $assertionService,
        Authorize $authorize,
        TranslatorInterface $translator,
        HelperPluginManager $helperPluginManager
    ) {
        $this->assertionService = $assertionService;
        $this->authorize = $authorize;
        $this->translator = $translator;

        $this->urlHelper = $helperPluginManager->get(Url::class);
        $this->serverUrlHelper = $helperPluginManager->get(ServerUrl::class);
    }

    public function __invoke(Session $session): SessionSpreadsheet
    {
        $this->session = $session;
        $this->spreadsheet = new Spreadsheet();
        //$this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        //Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);

        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->setTitle($this->translator->translate('txt-evaluation-report'));
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(true);
        $sheet->getPageSetup()->setFitToHeight(false);

        // Header
        $columns = [
            'A' => $this->translator->translate('txt-time'),
            'B' => $this->translator->translate('txt-idea'),
            'C' => $this->translator->translate('txt-title'),
            'D' => $this->translator->translate('txt-challenges'),
            'E' => $this->translator->translate('txt-notes'),
        ];
        \end($columns);
        $lastColumn = \key($columns);
        foreach (\range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A2:' . $lastColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setSize(18);
        $sheet->setCellValue(
            'A1',
            \sprintf('%s / %s', $session->getSession(), $session->getDate()->format('Y-m-d H:i'))
        );

        // Freeze the header
        $sheet->freezePane('A3');
        $sheet->fromArray($columns, null, 'A2');

        // Data
        $row = 3;
        foreach ($session->getIdeaSession() as $ideaSession) {
            //Check access to the idea
            $this->assertionService->addResource($ideaSession->getIdea(), \Project\Acl\Assertion\Idea\Idea::class);
            if (!$this->authorize->isAllowed($ideaSession->getIdea(), 'view')) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $ideaSession->getSchedule());

            $ideaLink = $this->urlHelper->__invoke('community/idea/view', ['docRef' => $ideaSession->getIdea()->getDocRef()]);
            $sheet->getCell('B'. $row)->getHyperlink()->setUrl(
                $this->serverUrlHelper->__invoke() . $ideaLink
            );

            $sheet->setCellValue('B' . $row, $ideaSession->getIdea()->getIdea());
            $sheet->setCellValue('C' . $row, $ideaSession->getIdea()->getTitle());
            $challenges = [];
            foreach ($ideaSession->getIdea()->getIdeaChallenge() as $challenge) {
                $challenges[] = $challenge->getChallenge();
            }
            $sheet->setCellValue('D' . $row, \implode(', ', $challenges));
            $row++;
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
                'Content-Disposition' => 'attachment; filename="' . $this->session->getSession() . '.xlsx"',
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

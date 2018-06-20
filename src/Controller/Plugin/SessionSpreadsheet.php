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

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Program\Entity\Call\Session;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class SessionExcel
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
     * @var TranslatorInterface
     */
    private $translator;


    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(Session $session): SessionSpreadsheet
    {
        $this->session     = $session;
        $this->spreadsheet = new Spreadsheet();
        //$this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        //Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);

        $sheet = $this->spreadsheet->getActiveSheet();
        //$sheet->setShowGridlines(false);
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
            'E' => $this->translator->translate('txt-description'),
        ];
        \end($columns);
        $lastColumn = \key($columns);
        foreach(\range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD');
        // Freeze the header
        $sheet->freezePane('A2');
        $sheet->fromArray($columns, null, 'A1');

        // Data
        $row = 2;
        foreach ($session->getIdeaSession() as $ideaSession) {
            $sheet->setCellValue('A' . $row, $ideaSession->getSchedule());
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

    /**
     * @return Response
     */
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
        $headers->addHeaders([
            'Content-Disposition' => 'attachment; filename="Session_' . $this->session->getId() . '.xlsx"',
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Length'      => $contentLength,
            'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
        ]);
        if ($gzip) {
            $headers->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($headers);

        return $response;
    }

    /**
     * @return Spreadsheet
     */
    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}

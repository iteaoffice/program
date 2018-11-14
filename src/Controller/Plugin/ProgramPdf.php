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

use setasign\Fpdi\TcpdfFpdi;

/**
 * Class ProgramPdf
 *
 * @package Program\Controller\Plugin
 */
final class ProgramPdf extends TcpdfFpdi
{
    /**
     * "Remembers" the template id of the imported page.
     */
    protected $_tplIdx;
    /**
     * @var string Location of the template
     */
    protected $template;

    /**
     * Draw an imported PDF logo on every page.
     */
    public function header()
    {
        if (null === $this->_tplIdx) {
            if (!file_exists($this->template)) {
                throw new \InvalidArgumentException(sprintf('Template %s cannot be found', $this->template));
            }
            $this->setSourceFile($this->template);
            $this->_tplIdx = $this->importPage(1);
        }
        $this->useTemplate($this->_tplIdx, 0, 0);
        $this->SetFont('freesans', 'N', 15);
        $this->SetTextColor(0);
        $this->SetXY(15, 5);
    }

    public function footer()
    {
        // emtpy method body
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param            $header
     * @param            $data
     * @param array|null $width
     * @param bool       $lastRow
     */
    public function coloredTable($header, $data, array $width = null, $lastRow = false): void
    {
        // Colors, line width and bold font
        $this->SetDrawColor(205, 205, 205);
        $this->SetFillColor(255, 255, 255);
        $this->SetLineWidth(0.1);
        $this->SetFont('', 'B');

        $w = $width;
        // Header
        if (null === $w) {
            $w = [40, 35, 40, 45, 40];
        }

        $num_headers = \count($header);

        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'l', 1);
        }


        if ($num_headers === 0) {
            $this->Cell(array_sum($w), 0, '', 'B');
        }

        $this->Ln();


        // Color and font restoration
        $this->SetFillColor(249, 249, 249);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        $rowCounter = 1;
        foreach ($data as $row) {
            $counter = 0;

            foreach ($row as $column) {
                if ($lastRow && $rowCounter === (\count($data))) {
                    $this->SetFont('', 'B');
                }


                $this->MultiCell($w[$counter], 8, $column, 1, 'L', $fill, 0, '', '', true, 0, false, true, 8, 'M');
                $counter++;
            }
            $rowCounter++;
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
    }
}

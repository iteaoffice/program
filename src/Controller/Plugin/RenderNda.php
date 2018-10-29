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

use Contact\Service\ContactService;
use Program\Entity\Nda;
use Program\Options\ModuleOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZfcTwig\View\TwigRenderer;

/**
 * Class RenderNda
 *
 * @package Program\Controller\Plugin
 */
final class RenderNda extends AbstractPlugin
{
    /**
     * @var TwigRenderer
     */
    private $renderer;
    /**
     * @var ModuleOptions
     */
    private $moduleOptions;
    /**
     * @var ContactService
     */
    private $contactService;

    public function __construct(TwigRenderer $renderer, ModuleOptions $moduleOptions, ContactService $contactService)
    {
        $this->renderer = $renderer;
        $this->moduleOptions = $moduleOptions;
        $this->contactService = $contactService;
    }

    public function renderForCall(Nda $nda): ProgramPdf
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->moduleOptions->getNdaTemplate());
        $pdf->AddPage();

        $pdf->SetFontSize(9);
        $pdf->SetXY(14, 50);
        $pdf->Write(0, 'Name:');
        $pdf->SetXY(77, 50);
        $pdf->Write(0, 'Date:');
        $pdf->SetXY(118, 50);
        $pdf->Write(0, 'Our reference:');

        /*
         * Write the contact details
         */
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $nda->getContact()->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $this->contactService->parseOrganisation($nda->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date('d-m-Y'));
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        $pdf->SetFontSize(7.5);
        /*
         * Use the NDA object to render the filename
         */
        $pdf->Write(0, $nda->parseFileName());
        $ndaContent = $this->renderer->render(
            'program/pdf/nda-call',
            [
                'contact'        => $nda->getContact(),
                'call'           => $nda->parseCall(),
                'contactService' => $this->contactService,
            ]
        );
        $pdf->writeHTMLCell(0, 0, 14, 70, $ndaContent);
        /*
         * Signage block
         */

        $pdf->SetXY(14, 260);
        $pdf->Write(0, 'Name:');
        $pdf->SetXY(100, 260);
        $pdf->Write(0, 'Date of Signature:');
        $pdf->SetXY(14, 270);
        $pdf->Write(0, 'Function:');
        $pdf->SetXY(100, 270);
        $pdf->Write(0, 'Signature:');
        $pdf->Line(130, 275, 190, 275);
        $pdf->Line(30, 265, 90, 265);
        $pdf->Line(130, 265, 190, 265);
        $pdf->Line(30, 275, 90, 275);

        return $pdf;
    }

    public function render(Nda $nda): ProgramPdf
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->moduleOptions->getNdaTemplate());
        $pdf->AddPage();

        $pdf->SetFontSize(9);
        $pdf->SetXY(14, 50);
        $pdf->Write(0, 'Name:');
        $pdf->SetXY(77, 50);
        $pdf->Write(0, 'Date:');
        $pdf->SetXY(118, 50);
        $pdf->Write(0, 'Our reference:');

        /*
         * Write the contact details
         */
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $nda->getContact()->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $this->contactService->parseOrganisation($nda->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date('Y-m-d'));

        $pdf->SetFontSize(7.5);
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        $pdf->Write(0, $nda->parseFileName());
        $ndaContent = $this->renderer->render(
            'program/pdf/nda-general',
            [
                'contact'        => $nda->getContact(),
                'contactService' => $this->contactService,
            ]
        );
        $pdf->writeHTMLCell(0, 0, 14, 70, $ndaContent);
        /*
         * Signage block
         */
        $pdf->SetXY(14, 255);
        $pdf->Write(0, 'Undersigned');
        $pdf->SetXY(14, 260);
        $pdf->Write(0, 'Name:');
        $pdf->SetXY(100, 260);
        $pdf->Write(0, 'Date of Signature:');
        $pdf->SetXY(14, 270);
        $pdf->Write(0, 'Function:');
        $pdf->SetXY(100, 270);
        $pdf->Write(0, 'Signature:');
        $pdf->Line(130, 275, 190, 275);
        $pdf->Line(30, 265, 90, 265);
        $pdf->Line(130, 265, 190, 265);
        $pdf->Line(30, 275, 90, 275);

        return $pdf;
    }
}

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
use Program\Entity\Doa;
use Program\Options\ModuleOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZfcTwig\View\TwigRenderer;

/**
 * Class RenderDoa
 *
 * @package Program\Controller\Plugin
 */
final class RenderDoa extends AbstractPlugin
{
    /**
     * @var TwigRenderer
     */
    protected $renderer;
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * RenderDoa constructor.
     *
     * @param TwigRenderer   $renderer
     * @param ModuleOptions  $moduleOptions
     * @param ContactService $contactService
     */
    public function __construct(TwigRenderer $renderer, ModuleOptions $moduleOptions, ContactService $contactService)
    {
        $this->renderer = $renderer;
        $this->moduleOptions = $moduleOptions;
        $this->contactService = $contactService;
    }


    /**
     * @param Doa $doa
     *
     * @return ProgramPdf
     */
    public function __invoke(Doa $doa): ProgramPdf
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->moduleOptions->getDoaTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(9);

        /*
         * Write the contact details
         */
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $doa->getContact()->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $this->contactService->parseOrganisation($doa->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date('Y-m-d'));
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        /*
         * Use the NDA object to render the filename
         */
        $pdf->Write(0, $doa->parseFileName());
        $ndaContent = $this->renderer->render(
            'program/pdf/doa-program',
            [
                'contact'        => $doa->getContact(),
                'program'        => $doa->getProgram(),
                'contactService' => $this->contactService,
            ]
        );
        $pdf->writeHTMLCell(0, 0, 14, 70, $ndaContent);
        /*
         * Signage block
         */
        $pdf->SetXY(14, 250);
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

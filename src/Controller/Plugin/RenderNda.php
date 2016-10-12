<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Controller\Plugin;

use Contact\Service\ContactService;
use General\Service\GeneralService;
use Program\Entity\Nda;
use Program\Options\ModuleOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcTwig\View\TwigRenderer;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class RenderNda extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param Nda $nda
     *
     * @return ProgramPdf
     */
    public function renderForCall(Nda $nda)
    {
        /**
         * @var $pdf \TCPDF
         */
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getNdaTemplate());
        $pdf->AddPage();

        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');

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
        $pdf->Write(0, $this->getContactService()->parseOrganisation($nda->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date("d-m-Y"));
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        $pdf->SetFontSize(7.5);
        /*
         * Use the NDA object to render the filename
         */
        $pdf->Write(0, $nda->parseFileName());
        $ndaContent = $twig->render('program/pdf/nda-call', [
            'contact'        => $nda->getContact(),
            'call'           => $nda->getCall(),
            'contactService' => $this->getContactService()
        ]);
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

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get(ModuleOptions::class);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Gateway to the Contact Service.
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get(ContactService::class);
    }

    /**
     * Render a NDA not bound to a call.
     *
     * @param Nda $nda
     *
     * @return ProgramPdf
     */
    public function render(Nda $nda)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getNdaTemplate());
        $pdf->AddPage();

        /**
         * @var $twig TwigRenderer
         */
        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');

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
        $pdf->Write(0, $this->getContactService()->parseOrganisation($nda->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date("Y-m-d"));

        $pdf->SetFontSize(7.5);
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        $pdf->Write(0, $nda->parseFileName());
        $ndaContent = $twig->render('program/pdf/nda-general', [
            'contact'        => $nda->getContact(),
            'contactService' => $this->getContactService()
        ]);
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

    /**
     * Gateway to the General Service.
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }
}

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Controller\Plugin;

use Program\Entity\Nda;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use General\Service\GeneralService;
use Program\Options\ModuleOptions;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Program\Entity\Call\Call;

/**
 * Special plugin to produce an array with the evaluation
 *
 * Class CreateEvaluation
 * @package Content\Controller\Plugin
 */
class RenderNda extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Render a NDA triggered for a call
     *
     * @param Contact $contact
     * @param Call    $call
     *
     * @return ProgramPdf
     */
    public function renderCall(Contact $contact, Call $call)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getNdaTemplate());

        $pdf->addPage();
        $pdf->SetFontSize(9);

        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');

        /**
         * Write the contact details
         */
        $contactService = $this->getContactService()->setContact($contact);
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $contactService->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $contactService->parseOrganisation());


        /**
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date("Y-m-d"));

        /**
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        /**
         * Use the NDA object to render the filename
         */
        $nda = new Nda();
        $nda->setContact($contact);
        $nda->setCall($call);
        $pdf->Write(0, $nda->parseFileName());

        $ndaContent = $twig->render(
            'program/pdf/nda-call',
            array(
                'contact' => $nda->getContact(),
                'call'    => $nda->getCall(),
            )
        );

        $pdf->writeHTMLCell(0, 0, 14, 70, $ndaContent);

        /**
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
     * Render a NDA not bound to a call
     *
     * @param Nda $nda
     *
     * @return ProgramPdf
     */
    public function render(Nda $nda)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getNdaTemplate());

        $pdf->addPage();
        $pdf->SetFontSize(10);

        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');

        /**
         * Write the contact details
         */
        $contactService = $this->getContactService()->setContact($contact);
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $contactService->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $contactService->parseOrganisation());


        /**
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date("Y-m-d"));

        /**
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        $pdf->Write(0, $nda->parseFileName());

        $ndaContent = $twig->render(
            'program/pdf/nda-general',
            array(
                'contact' => $contact,
            )
        );

        $pdf->writeHTMLCell(0, 0, 14, 70, $ndaContent);

        /**
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
     * Gateway to the General Service
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get('general_general_service');
    }

    /**
     * Gateway to the Contact Service
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_contact_service');
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get('program_module_options');
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
}
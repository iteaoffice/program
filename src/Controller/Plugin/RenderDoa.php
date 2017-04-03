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

namespace Program\Controller\Plugin;

use Contact\Service\ContactService;
use General\Service\GeneralService;
use Program\Entity\Doa;
use Program\Options\ModuleOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

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
class RenderDoa extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param Doa $doa
     *
     * @return ProgramPdf
     */
    public function __invoke(Doa $doa)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getDoaTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(9);
        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');
        /*
         * Write the contact details
         */
        $pdf->SetXY(14, 55);
        $pdf->Write(0, $doa->getContact()->parseFullName());
        $pdf->SetXY(14, 60);
        $pdf->Write(0, $this->getContactService()->parseOrganisation($doa->getContact()));
        /*
         * Write the current date
         */
        $pdf->SetXY(77, 55);
        $pdf->Write(0, date("Y-m-d"));
        /*
         * Write the Reference
         */
        $pdf->SetXY(118, 55);
        /*
         * Use the NDA object to render the filename
         */
        $pdf->Write(0, $doa->parseFileName());
        $ndaContent = $twig->render(
            'program/pdf/doa-program',
            [
                'contact'        => $doa->getContact(),
                'program'        => $doa->getProgram(),
                'contactService' => $this->getContactService(),
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
     * Gateway to the General Service.
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }
}

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Controller\Plugin;

use Contact\Service\ContactService;
use General\Service\GeneralService;
use Program\Entity\Doa;
use Program\Options\ModuleOptions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
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
    public function renderForDoa(Doa $doa)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getDoaTemplate());
        $pdf->addPage();
        $pdf->SetFontSize(9);
        $twig = $this->getServiceLocator()->get('ZfcTwigRenderer');
        /**
         * Write the contact details
         */
        $contactService = $this->getContactService()->setContact($doa->getContact());
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
        $pdf->Write(0, $doa->parseFileName());
        $ndaContent = $twig->render(
            'program/pdf/doa-program',
            [
                'contact' => $doa->getContact(),
                'program' => $doa->getProgram(),
            ]
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
     * Gateway to the General Service
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }
}

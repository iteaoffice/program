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
use Program\Entity\Call\Session;
use Program\Options\ModuleOptions;
use Project\Service\IdeaService;
use Zend\I18n\View\Helper\Translate;
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
class RenderSession extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

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
     * @return array|object
     */
    public function getIdeaService()
    {
        return $this->getServiceLocator()->get(IdeaService::class);
    }

    /**
     * @param Session $session
     * @return ProgramPdf
     */
    public function __invoke(Session $session)
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->getModuleOptions()->getBlankTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(8);
        $pdf->SetTopMargin(30);

        $pdf->writeHTMLCell(
            0,
            0,
            '',
            '',
            '<h1 style="color: #00a651">' . $session->getSession() . '</h1>',
            0,
            1,
            0,
            true,
            '',
            true
        );
        $pdf->writeHTMLCell(
            0,
            0,
            '',
            '',
            $session->getDate()->format("d-m-Y"),
            0,
            1,
            0,
            true,
            '',
            true
        );
        $pdf->Ln();
        $pdf->Line(10, 42, 200, 42, ['color' => [0, 166, 81]]);

        $pdf->Ln();

        //Funding information
        $header = [
            $this->translate("txt-time"),
            $this->translate("txt-no"),
            $this->translate("txt-idea"),
            $this->translate("txt-title"),
            $this->translate("txt-presenter"),

        ];

        $pitches = [];

        foreach ($session->getIdeaSession() as $ideaSession) {
            $pitches[] = [
                $ideaSession->getSchedule(),
                $ideaSession->getIdea()->getNumber(),
                $ideaSession->getIdea()->getIdea(),
                $ideaSession->getIdea()->getTitle(),
                $ideaSession->getIdea()->getContact()->getDisplayName()
            ];
        }

        $pdf->coloredTable($header, $pitches, [15, 10, 25, 110, 30]);

        return $pdf;
    }

    /**
     *
     */

    /**
     * Gateway to the General Service.
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get(GeneralService::class);
    }

    /**
     * Proxy for the flash messenger helper to have the string translated earlier.
     *
     * @param $string
     *
     * @return string
     */
    protected function translate($string)
    {
        /**
         * @var $translate Translate
         */
        $translate = $this->getServiceLocator()->get('ViewHelperManager')->get('translate');

        return $translate($string);
    }
}

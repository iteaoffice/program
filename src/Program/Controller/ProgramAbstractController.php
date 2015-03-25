<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */

namespace Program\Controller;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Service\ContactService;
use General\Service\GeneralService;
use General\Service\GeneralServiceAwareInterface;
use Organisation\Service\OrganisationService;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\CallServiceAwareInterface;
use Program\Service\FormService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\ProgramService;
use Program\Service\ProgramServiceAwareInterface;
use Project\Service\ProjectService;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 *
 * @link       http://debranova.org
 *
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 */
abstract class ProgramAbstractController extends AbstractActionController implements
    FormServiceAwareInterface,
    CallServiceAwareInterface,
    ProgramServiceAwareInterface,
    GeneralServiceAwareInterface
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @return FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return ProgramAbstractController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Gateway to the Program Service.
     *
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->programService;
    }

    /**
     * @param $programService
     *
     * @return ProgramAbstractController
     */
    public function setProgramService(ProgramService $programService)
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * Gateway to the Call Service.
     *
     * @return CallService
     */
    public function getCallService()
    {
        return $this->callService;
    }

    /**
     * @param $callService
     *
     * @return ProgramAbstractController
     */
    public function setCallService(CallService $callService)
    {
        $this->callService = $callService;

        return $this;
    }

    /**
     * Gateway to the General Service.
     *
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->generalService;
    }

    /**
     * @param $generalService
     *
     * @return ProgramAbstractController
     */
    public function setGeneralService(GeneralService $generalService)
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * Gateway to the Organisation Service.
     *
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceLocator()->get('organisation_organisation_service');
    }

    /**
     * @return \Project\Service\ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get(ProjectService::class);
    }

    /**
     * @return \Program\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get('program_module_options');
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return $this;
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;

        return $this;
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
        /*
         * @var Translate
         */
        $translate = $this->getServiceLocator()->get('ViewHelperManager')->get('translate');

        return $translate($string);
    }
}

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

namespace Program\Controller;

use Admin\Service\AdminService;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\EmailService;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Controller\Plugin;
use Program\Controller\Plugin\RenderSession;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\HelperPluginManager;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 * @method      RenderSession renderSession($session)
 * @method      Plugin\GetFilter getProgramFilter()
 * @method      Plugin\RenderDoa renderDoa($doa)
 * @method      Plugin\RenderNda renderNda()
 * @method      Plugin\CreateCallFundingOverview createCallFundingOverview()
 * @method      Plugin\CreateFundingDownload createFundingDownload()
 */
abstract class ProgramAbstractController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var AdminService
     */
    protected $adminService;
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
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var VersionService
     */
    protected $versionService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var HelperPluginManager
     */
    protected $viewHelperManager;
    /**
     * @var EmailService;
     */
    protected $emailService;

    /**
     * @return FormService
     */
    public function getFormService(): FormService
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
    public function setProgramService(ProgramService $programService): ProgramAbstractController
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
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->projectService;
    }

    /**
     * @param  ProjectService $projectService
     *
     * @return ProgramAbstractController
     */
    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->organisationService;
    }

    /**
     * @param  OrganisationService $organisationService
     *
     * @return ProgramAbstractController
     */
    public function setOrganisationService(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return AdminService
     */
    public function getAdminService()
    {
        return $this->adminService;
    }

    /**
     * @param  AdminService $adminService
     *
     * @return ProgramAbstractController
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;

        return $this;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     *
     * @return ProgramAbstractController
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return ProgramAbstractController
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return VersionService
     */
    public function getVersionService()
    {
        return $this->versionService;
    }

    /**
     * @param VersionService $versionService
     *
     * @return ProgramAbstractController
     */
    public function setVersionService($versionService)
    {
        $this->versionService = $versionService;

        return $this;
    }

    /**
     * @return EmailService
     */
    public function getEmailService(): ?EmailService
    {
        return $this->emailService;
    }

    /**
     * @param EmailService $emailService
     *
     * @return ProgramAbstractController
     */
    public function setEmailService(EmailService $emailService): ProgramAbstractController
    {
        $this->emailService = $emailService;

        return $this;
    }

    /**
     * Proxy for the flash messenger helper to have the string translated earlier.
     *
     * @param $string
     *
     * @return string
     */
    protected function translate($string): string
    {
        /**
         * @var $translate Translate
         */
        $translate = $this->getViewHelperManager()->get('translate');

        return $translate($string);
    }

    /**
     * @return HelperPluginManager
     */
    public function getViewHelperManager(): HelperPluginManager
    {
        return $this->viewHelperManager;
    }

    /**
     * @param HelperPluginManager $viewHelperManager
     *
     * @return ProgramAbstractController
     */
    public function setViewHelperManager(HelperPluginManager $viewHelperManager): ProgramAbstractController
    {
        $this->viewHelperManager = $viewHelperManager;

        return $this;
    }
}

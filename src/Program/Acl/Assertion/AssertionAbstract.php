<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */

namespace Program\Acl\Assertion;

use Admin\Entity\Access;
use Admin\Service\AdminService;
use Admin\Service\AdminServiceAwareInterface;
use Contact\Service\ContactService;
use Contact\Service\ContactServiceAwareInterface;
use Doctrine\ORM\PersistentCollection;
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;
use Organisation\Service\OrganisationService;
use Organisation\Service\OrganisationServiceAwareInterface;
use Program\Service\CallService;
use Program\Service\CallServiceAwareInterface;
use Program\Service\ProgramService;
use Program\Service\ProgramServiceAwareInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a link to an document.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */
abstract class AssertionAbstract implements
    AssertionInterface,
    OrganisationServiceAwareInterface,
    AdminServiceAwareInterface,
    ServiceLocatorAwareInterface,
    ProgramServiceAwareInterface,
    CallServiceAwareInterface,
    ContactServiceAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var AdminService
     */
    protected $adminService;
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var array
     */
    protected $accessRoles = [];

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get("Application")->getMvcEvent()->getRouteMatch();
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AssertionAbstract
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasContact()
    {
        return !$this->getContactService()->isEmpty();
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * The contact service.
     *
     * @param ContactService $contactService
     *
     * @return $this;
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;
        if ($this->contactService->isEmpty() && $this->getServiceLocator()->get('zfcuser_auth_service')->hasIdentity()
        ) {
            $this->contactService->setContact(
                $this->getServiceLocator()->get('zfcuser_auth_service')->getIdentity()
            );
        }

        return $this;
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->programService;
    }

    /**
     * @param ProgramService $programService
     *
     * @return AssertionAbstract
     */
    public function setProgramService(ProgramService $programService)
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->callService;
    }

    /**
     * @param CallService $callService
     *
     * @return AssertionAbstract
     */
    public function setCallService(CallService $callService)
    {
        $this->callService = $callService;

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
     * @param AdminService $adminService
     *
     * @return AssertionAbstract
     */
    public function setAdminService(AdminService $adminService)
    {
        $this->adminService = $adminService;

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
     * @param OrganisationService $organisationService
     *
     * @return AssertionAbstract
     */
    public function setOrganisationService(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return OrganisationAssertion
     */
    public function getOrganisationAssert()
    {
        return $this->getServiceLocator()->get(OrganisationAssertion::class);
    }

    /**
     * Returns true when a role or roles have access.
     *
     * @param string|PersistentCollection $access
     *
     * @return boolean
     */
    public function rolesHaveAccess($access)
    {
        $accessRoles = $this->prepareAccessRoles($access);
        if (sizeof($accessRoles) === 0) {
            return true;
        }
        foreach ($accessRoles as $accessRole) {
            if (strtolower($accessRole->getAccess()) === strtolower(Access::ACCESS_PUBLIC)) {
                return true;
            }
            if ($this->hasContact()) {
                if (in_array(
                    strtolower($accessRole->getAccess()),
                    $this->getAdminService()->findAccessRolesByContactAsArray($this->getContactService()->getContact())
                )
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $access
     *
     * @return Access[]
     */
    protected function prepareAccessRoles($access)
    {
        if (!$access instanceof PersistentCollection) {
            /*
             * We only have a string, so we need to lookup the role
             */
            $access = [
                $this->getAdminService()->findAccessByName($access),
            ];
        }

        return $access;
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        if (empty($this->accessRoles) && !$this->getContactService()->isEmpty()) {
            $this->accessRoles = $this->getAdminService()->findAccessRolesByContactAsArray(
                $this->getContactService()->getContact()
            );
        }

        return $this->accessRoles;
    }
}

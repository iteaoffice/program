<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Acl
 * @subpackage  Assertion
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Acl\Assertion;

use Contact\Service\ContactService;
use Program\Entity\Nda as NdaEntity;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Program
 * @package Program\Acl\Assertion
 */
class Nda implements AssertionInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var array
     */
    protected $accessRoles = array();

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->callService    = $this->serviceManager->get("program_call_service");
        $this->programService = $this->serviceManager->get("program_program_service");
        $this->contactService = $this->serviceManager->get("contact_contact_service");

        /**
         * Store locally in the object the contact information
         */
        if ($this->serviceManager->get('zfcuser_auth_service')->hasIdentity()) {
            $this->contactService->setContact($this->serviceManager->get('zfcuser_auth_service')->getIdentity());
            $this->accessRoles = $this->contactService->getContact()->getRoles();
        }
    }

    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl               $acl
     * @param RoleInterface     $role
     * @param ResourceInterface $resource
     * @param string            $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $routeMatch = $this->serviceManager->get("Application")->getMvcEvent()->getRouteMatch();

        $id = $routeMatch->getParam('id');
        /**
         * When the privilege is_null (not given by the isAllowed helper), get it from the routeMatch
         */
        if (is_null($privilege)) {
            $privilege = $routeMatch->getParam('privilege');
        }

        if (!$resource instanceof NdaEntity && !is_null($id)) {
            $resource = $this->programService->findEntityById('Nda', $id);
        }

        switch ($privilege) {
            case 'upload':
                return !is_null($this->contactService);

            case 'replace':
                /**
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */

                return is_null($resource->getDateApproved()) && $resource->getContact()->getId(
                ) === $this->contactService->getContact()->getId();

            case 'render':

                if (is_null($this->contactService)) {
                    return false;
                }

                /**
                 * When a call is set, check if that call has the right status
                 */
                if (!is_null($routeMatch->getParam('call-id'))) {
                    $this->callService->setCallId($routeMatch->getParam('call-id'));

                    return $this->callService->getCallStatus()->result !== CallService::UNDEFINED;
                }

                return true;

            case 'download':
            case 'view':
                return $resource->getContact()->getId() === $this->contactService->getContact()->getId();
                break;
        }

        return false;
    }
}

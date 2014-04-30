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
use Program\Entity\Doa as DoaEntity;
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
class Doa implements AssertionInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
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

        if (!$resource instanceof DoaEntity && !is_null($id)) {
            $resource = $this->programService->findEntityById('Doa', $id);
        }

        switch ($privilege) {
            case 'upload':

                break;

            case 'replace':
                /**
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */

                return is_null($resource->getDateApproved()) && $resource->getContact()->getId(
                ) === $this->contactService->getContact()->getId();

                break;

            case 'render':
                return !is_null($this->contactService);

                break;

            case 'download':
            case 'view':
                return $resource->getContact()->getId() === $this->contactService->getContact()->getId();
                break;
        }

        return false;
    }
}

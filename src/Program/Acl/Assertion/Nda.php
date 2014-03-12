<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Publication
 * @package     Acl
 * @subpackage  Assertion
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Acl\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

use Zend\ServiceManager\ServiceManager;
use Program\Service\ProgramService;
use Contact\Entity\Contact;

/**
 * Class Result
 * @package Program\Acl\Assertion
 */
class Nda implements AssertionInterface
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
     * @var Contact
     */
    protected $contact;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->programService = $this->serviceManager->get("program_program_service");
        if ($this->serviceManager->get('zfcuser_auth_service')->hasIdentity()) {
            $this->contact = $this->serviceManager->get('zfcuser_auth_service')->getIdentity();
        } else {
            $this->contact = null;
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
        $id = $this->serviceManager->get("Application")->getMvcEvent()->getRouteMatch()->getParam('id');

        $nda = $this->programService->findEntityById('Nda', $id);

        if (is_null($nda)) {
            return false;
        }

        if (is_null($this->contact)) {
            return false;
        }

        return $this->contact->getId() === $nda->getContact()->getId();
    }
}

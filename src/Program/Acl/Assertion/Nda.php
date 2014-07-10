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

use Program\Entity\Nda as NdaEntity;
use Program\Service\CallService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Program
 * @package Program\Acl\Assertion
 */
class Nda extends AssertionAbstract
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        $id = $this->getRouteMatch()->getParam('id');

        /**
         * When the privilege is_null (not given by the isAllowed helper), get it from the routeMatch
         */
        if (is_null($privilege)) {
            $privilege = $this->getRouteMatch()->getParam('privilege');
        }
        /**
         * @var $resource NdaEntity
         */
        if (!$resource instanceof NdaEntity && !is_null($id)) {
            $resource = $this->getProgramService()->findEntityById('Nda', $id);
        }
        switch ($privilege) {
            case 'upload':
                return $this->hasContact();
            case 'replace':
                /**
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */

                return is_null($resource->getDateApproved()) &&
                $resource->getContact()->getId() === $this->getContactService()->getContact()->getId();
            case 'render':
                if (!$this->hasContact()) {
                    return false;
                }
                /**
                 * When a call is set, check if that call has the right status
                 * The call can be found in 1 ways, or via the $id, or via the resource.
                 * The resource has goes first
                 */
                if ($resource instanceof NdaEntity && !is_null($resource->getCall())) {
                    $this->getCallService()->setCall($resource->getCall());
                } elseif (!is_null($id)) {
                    $this->getCallService()->setCallId($id);
                }

                //We have no 2 methods to get the call, if the call is set check if the status is correct
                if (!$this->getCallService()->isEmpty()) {
                    return $this->getCallService()->getCallStatus()->result !== CallService::UNDEFINED;
                }

                return true;
            case 'download':
            case 'view':
                return $resource->getContact()->getId() === $this->getContactService()->getContact()->getId();
        }

        return false;
    }
}

<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Acl\Assertion;

use Admin\Entity\Access;
use Program\Entity\Nda as NdaEntity;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Program.
 */
class Nda extends AssertionAbstract
{
    /**
     * Returns true if and only if the assertion conditions are met.
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $nda, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $nda
     * @param string $privilege
     *
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $nda = null, $privilege = null): bool
    {
        $this->setPrivilege($privilege);
        $id = $this->getId();

        /*
         * @var $nda NdaEntity
         */
        if (!$nda instanceof NdaEntity && !\is_null($id)) {
            /** @var NdaEntity $nda */
            $nda = $this->getProgramService()->findEntityById(NdaEntity::class, $id);
        }

        switch ($this->getPrivilege()) {
            case 'submit':
                return $this->hasContact() && !\is_null($this->getContact()->getContactOrganisation());
            case 'replace':
                /*
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */

                return \is_null($nda->getDateApproved())
                    && $nda->getContact()->getId() === $this->getContact()->getId();
            case 'render':
                if (!$this->hasContact() || \is_null($this->getContact()->getContactOrganisation())) {
                    return false;
                }
                /*
                 * When a call is set, check if that call has the right status
                 * The call can be found in 1 ways, or via the $id, or via the resource.
                 * The resource has goes first
                 */
                $call = null;
                if ($nda instanceof NdaEntity && null !== $nda->getCall()) {
                    $call = $nda->getCall();
                } elseif (!\is_null($callId = $this->getRouteMatch()->getParam('callId'))) {
                    $call = $this->getCallService()->findCallById($callId);
                }

                //We have no 2 methods to get the call, if the call is set check if the status is correct
                if (null !== $call) {
                    return true;

                    //return $this->getCallService()->getCallStatus($call)->result !== CallService::UNDEFINED;
                }

                return true;
            case 'download':
            case 'view':
                if (!$this->hasContact()) {
                    return false;
                }

                if ($nda->getContact()->getId() === $this->getContact()->getId()) {
                    return true;
                }

                return $this->rolesHaveAccess([Access::ACCESS_OFFICE]);
            case 'render-admin':
            case 'view-admin':
            case 'edit-admin':
            case 'approval-admin':
            case 'upload-admin':
                return $this->rolesHaveAccess([Access::ACCESS_OFFICE]);
        }

        return false;
    }
}

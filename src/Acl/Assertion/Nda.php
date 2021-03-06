<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Acl\Assertion;

use Admin\Entity\Access;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Program\Entity;

/**
 * Class Nda
 *
 * @package Program\Acl\Assertion
 */
final class Nda extends AbstractAssertion
{
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $nda = null, $privilege = null): bool
    {
        $this->setPrivilege($privilege);
        $id = $this->getId();

        /*
         * @var $nda Entity\Nda
         */
        if (! $nda instanceof Entity\Nda && null !== $id) {
            /** @var Entity\Nda $nda */
            $nda = $this->programService->find(Entity\Nda::class, (int)$id);
        }

        switch ($this->getPrivilege()) {
            case 'submit':
                return $this->hasContact() && null !== $this->contact->getContactOrganisation();
            case 'replace':
                /*
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */

                return null === $nda->getDateApproved() && $nda->getContact()->getId() === $this->contact->getId();
            case 'render':
                if (! $this->hasContact() || null === $this->contact->getContactOrganisation()) {
                    return false;
                }
                /*
                 * When a call is set, check if that call has the right status
                 * The call can be found in 1 ways, or via the $id, or via the resource.
                 * The resource has goes first
                 */
                $call = null;
                if ($nda instanceof Entity\Nda && null !== $nda->getCall()) {
                    $call = $nda->getCall();
                } elseif (null !== ($callId = $this->getRouteMatch()->getParam('callId'))) {
                    $call = $this->callService->findCallById((int)$callId);
                }

                //We have no 2 methods to get the call, if the call is set check if the status is correct
                if (null !== $call) {
                    return true;
                }

                return true;
            case 'download':
            case 'view':
                if (! $this->hasContact()) {
                    return false;
                }

                if ($nda->getContact()->getId() === $this->contact->getId()) {
                    return true;
                }

                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
            case 'render-admin':
            case 'view-admin':
            case 'edit-admin':
            case 'approval-admin':
            case 'upload':
                return $this->rolesHaveAccess(Access::ACCESS_OFFICE);
        }

        return false;
    }
}

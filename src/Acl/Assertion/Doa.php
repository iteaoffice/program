<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Acl\Assertion;

use Admin\Entity\Access;
use Program\Entity;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Class Doa
 *
 * @package Program\Acl\Assertion
 */
final class Doa extends AbstractAssertion
{
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $doa = null, $privilege = null): bool
    {
        $this->setPrivilege($privilege);
        $id = $this->getId();

        if (! $doa instanceof Entity\Doa && null !== $id) {
            $doa = $this->programService->find(Entity\Doa::class, (int)$id);
        }

        switch ($this->getPrivilege()) {
            case 'upload':
                /*
                 * For the upload we need to see if the user has access on the editing of the affiliation
                 * The affiliation can already be known, but if not grab it from the routeMatch
                 */
                $organisation = null;

                if ($doa instanceof Entity\Doa) {
                    $organisation = $doa->getOrganisation();
                }

                if (null === $organisation) {
                    /**
                     * The id can originate from two different params
                     */
                    if (null === $id) {
                        $id = $this->getRouteMatch()->getParam('organisationId');
                    }
                    $organisation = $this->organisationService->findOrganisationById((int)$id);
                }

                return $this->assert($acl, $role, $organisation, 'edit-community');
            case 'replace':
                /*
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */
                if ($this->rolesHaveAccess(Access::ACCESS_OFFICE)) {
                    return true;
                }

                return null === $doa->getDateApproved() && $doa->getContact()->getId() === $this->contact->getId();
            case 'render':
                return $this->hasContact();
            case 'download':
            case 'view':
                if ($this->rolesHaveAccess(Access::ACCESS_OFFICE)) {
                    return true;
                }

                return $doa->getContact()->getId() === $this->contact->getId();
        }

        return false;
    }
}

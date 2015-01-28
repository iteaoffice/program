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

use Program\Entity\Doa as DoaEntity;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Program
 * @package Program\Acl\Assertion
 */
class Doa extends AssertionAbstract
{
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
        $id = $this->getRouteMatch()->getParam('id');
        /**
         * When the privilege is_null (not given by the isAllowed helper), get it from the routeMatch
         */
        if (is_null($privilege)) {
            $privilege = $this->getRouteMatch()->getParam('privilege');
        }
        if (!$resource instanceof DoaEntity && !is_null($id)) {
            $resource = $this->getProgramService()->findEntityById('Doa', $id);
        }
        switch ($privilege) {
            case 'upload':
                /**
                 * For the upload we need to see if the user has access on the editing of the affiliation
                 * The affiliation can already be known, but if not grab it from the routeMatch
                 */
                $organisation = null;
                if ($resource instanceof DoaEntity) {
                    $organisation = $resource->getOrganisation();
                }
                if (is_null($organisation)) {
                    /**
                     * The id can originate from two different params
                     */
                    if (!is_null($this->getRouteMatch()->getParam('id'))) {
                        $organisationId = $this->getRouteMatch()->getParam('id');
                    } else {
                        $organisationId = $this->getRouteMatch()->getParam('organisation-id');
                    }
                    $organisation = $this->getOrganisationService()->setOrganisationId(
                        $organisationId
                    )->getOrganisation();
                }

                return $this->getOrganisationAssert()->assert($acl, $role, $organisation, 'edit-community');
            case 'replace':
                /**
                 * For the replace we need to see if the user has access on the editing of the program
                 * and the acl should not be approved
                 */
                return is_null($resource->getDateApproved()) && $resource->getContact()->getId() ===
                $this->getContactService()->getContact()->getId();
            case 'render':
                return $this->hasContact();
            case 'download':
            case 'view':
                return $resource->getContact()->getId() === $this->contactService->getContact()->getId();
        }

        return false;
    }
}

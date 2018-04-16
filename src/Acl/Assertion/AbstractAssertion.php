<?php
/**
 * Jield BV all rights reserved
 *
 * @category   Admin
 *
 * @author     Johan van der Heide <info@jield.nl>
 * @copyright  Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 * @license    https://jield.nl/license.txt proprietary
 *
 * @link       https://jield.nl
 */

declare(strict_types=1);

namespace Program\Acl\Assertion;

use Admin\Entity\Access;
use Admin\Service\AdminService;
use Contact\Entity\Contact;
use Doctrine\ORM\PersistentCollection;
use Interop\Container\ContainerInterface;
use Organisation\Service\OrganisationService;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Router\Http\RouteMatch;

/**
 * Class AbstractAssertion
 *
 * @package Program\Acl\Assertion
 */
abstract class AbstractAssertion implements AssertionInterface
{
    /**
     * @var AdminService
     */
    protected $adminService;
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var string
     */
    protected $privilege;
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AbstractAssertion constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get('Application')->getRequest();

        $this->adminService = $container->get(AdminService::class);
        $this->programService = $container->get(ProgramService::class);
        $this->callService = $container->get(CallService::class);
        $this->organisationService = $container->get(OrganisationService::class);
        $this->contact = $container->get(AuthenticationService::class)->getIdentity();
    }


    /**
     * @param string $string
     *
     * @return bool
     */
    public function routeHasString(string $string): bool
    {
        return $this->hasRouteMatch() && \strpos($this->getRouteMatch()->getMatchedRouteName(), $string) !== false;
    }

    /**
     * @return bool
     */
    public function hasRouteMatch(): bool
    {
        return null !== $this->getRouteMatch()->getMatchedRouteName();
    }

    /**
     * @return RouteMatch
     */
    protected function getRouteMatch(): RouteMatch
    {
        $routeMatch = $this->container->get('Application')->getMvcEvent()->getRouteMatch();

        if (null !== $routeMatch) {
            return $routeMatch;
        }
        return new RouteMatch([]);
    }

    /**
     * @return string
     */
    public function getPrivilege(): string
    {
        /**
         * When the privilege is_null (not given by the isAllowed helper), get it from the routeMatch
         */
        if (null === $this->privilege) {
            $this->privilege = $this->getRouteMatch()->getParam(
                'privilege',
                $this->getRouteMatch()->getParam('action')
            );
        }

        return $this->privilege;
    }

    /**
     * @param string $privilege
     *
     * @return AbstractAssertion
     */
    public function setPrivilege(?string $privilege): AbstractAssertion
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        if (null !== $this->request->getPost('id')) {
            return (int)$this->request->getPost('id');
        }
        if (!$this->hasRouteMatch()) {
            return null;
        }
        if (null !== $this->getRouteMatch()->getParam('id')) {
            return (int)$this->getRouteMatch()->getParam('id');
        }

        return null;
    }

    /**
     * Returns true when a role or roles have access.
     *
     * @param string|PersistentCollection $accessRoleOrCollection
     *
     * @return boolean
     */
    public function rolesHaveAccess($accessRoleOrCollection): bool
    {
        $accessRoles = $this->prepareAccessRoles($accessRoleOrCollection);
        if (\count($accessRoles) === 0) {
            return true;
        }

        foreach ($accessRoles as $access) {
            if ($access === strtolower(Access::ACCESS_PUBLIC)) {
                return true;
            }
            if ($this->hasContact()
                && \in_array(
                    $access,
                    $this->adminService->findAccessRolesByContactAsArray($this->contact),
                    true
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|PersistentCollection $accessRoleOrCollection
     *
     * @return array
     */
    protected function prepareAccessRoles($accessRoleOrCollection): array
    {
        if (\is_string($accessRoleOrCollection)) {
            return [\strtolower($accessRoleOrCollection)];
        }

        $accessRoles = [];
        /** @var Access $access */
        if (\is_iterable($accessRoleOrCollection)) {
            foreach ($accessRoleOrCollection as $access) {
                $accessRoles[] = \strtolower($access->getAccess());
            }
        }

        return \array_unique($accessRoles);
    }

    /**
     * @return bool
     */
    public function hasContact(): bool
    {
        return null !== $this->contact;
    }
}

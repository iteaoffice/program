<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Publication
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2016 ITEA Office (http://itea3.org)
 */

namespace Program\Acl\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;
use Organisation\Service\OrganisationService;
use Program\Acl\Assertion\AssertionAbstract;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionInvokableAbstractFactory
 *
 * @package Affiliation\Acl\Factory
 */
class AssertionInvokableAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (class_exists($requestedName)
            && in_array(AssertionAbstract::class, class_parents($requestedName)));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /** @var $assertion AssertionAbstract */
        $assertion = new $requestedName();
        $assertion->setServiceLocator($serviceLocator);

        /** @var AdminService $adminService */
        $adminService = $serviceLocator->get(AdminService::class);
        $assertion->setAdminService($adminService);

        /** @var ContactService $contactService */
        $contactService = $serviceLocator->get(ContactService::class);
        $assertion->setContactService($contactService);

        //Inject the logged in user if applicable
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $serviceLocator->get('Application\Authentication\Service');
        if ($authenticationService->hasIdentity()) {
            $assertion->setContact($authenticationService->getIdentity());
        }

        /** @var OrganisationService $organisationService */
        $organisationService = $serviceLocator->get(OrganisationService::class);
        $assertion->setOrganisationService($organisationService);

        /** @var ProgramService $programService */
        $programService = $serviceLocator->get(ProgramService::class);
        $assertion->setProgramService($programService);

        /** @var CallService $callService */
        $callService = $serviceLocator->get(CallService::class);
        $assertion->setCallService($callService);

        /** @var OrganisationAssertion $organisationAssertion */
        $organisationAssertion = $serviceLocator->get(OrganisationAssertion::class);
        $assertion->setOrganisationAssertion($organisationAssertion);

        return $assertion;
    }
}

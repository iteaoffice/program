<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */
namespace Program\Acl\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Organisation\Acl\Assertion\Organisation as OrganisationAssertion;
use Organisation\Service\OrganisationService;
use Program\Acl\Assertion\AssertionAbstract;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AssertionFactory
 *
 * @package Affiliation\Acl\Factory
 */
class AssertionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $assertion AssertionAbstract */
        $assertion = new $requestedName($options);
        $assertion->setServiceLocator($container);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $assertion->setAdminService($adminService);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $assertion->setContactService($contactService);

        //Inject the logged in user if applicable
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get('Application\Authentication\Service');
        if ($authenticationService->hasIdentity()) {
            $assertion->setContact($authenticationService->getIdentity());
        }

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $assertion->setOrganisationService($organisationService);

        /** @var ProgramService $programService */
        $programService = $container->get(ProgramService::class);
        $assertion->setProgramService($programService);

        /** @var CallService $callService */
        $callService = $container->get(CallService::class);
        $assertion->setCallService($callService);

        /** @var OrganisationAssertion $organisationAssertion */
        $organisationAssertion = $container->get(OrganisationAssertion::class);
        $assertion->setOrganisationAssertion($organisationAssertion);

        return $assertion;
    }
}

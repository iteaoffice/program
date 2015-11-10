<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace ProgramTest\Service;

use Contact\Service\ContactService;
use Program\Service\ProgramService;
use ProgramTest\Bootstrap;

class ProgramServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $entityManager;
    /**
     * @var ProgramService;
     */
    protected $programService;
    /**
     * @var ContactService;
     */
    protected $contactService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->programService = new ProgramService();
        $this->programService->setServiceLocator($this->serviceManager);
        $this->contactService = new ContactService();
        $this->contactService->setServiceLocator($this->serviceManager);
    }

    public function testCanFindFunderByCountry()
    {
        $country = $this->entityManager->find("General\Entity\Country", 1);
        $funder = $this->programService->findFunderByCountry($country);
        foreach ($funder as $funderResult) {
            $this->assertInstanceOf('Program\\Entity\\Funder', $funderResult);
            $this->assertEquals($funderResult->getCountry()->getId(), 1);
        }
    }
}

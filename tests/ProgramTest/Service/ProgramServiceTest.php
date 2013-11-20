<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace ProjectTest\Service;

use Program\Service\ProgramService;
use Contact\Service\ContactService;
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
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->programService = new ProgramService();
        $this->programService->setServiceLocator($this->serviceManager);

        $this->contactService = new ContactService();
        $this->contactService->setServiceLocator($this->serviceManager);
    }


    public function testCanFindFunderByCountry()
    {
        $country = $this->entityManager->find("General\Entity\Country", 1);
        $funder  = $this->programService->findFunderByCountry($country);
        foreach ($funder as $funderResult) {
            $this->assertInstanceOf('Program\\Entity\\Funder', $funderResult);
            $this->assertEquals($funderResult->getCountry()->getId(), 1);
        }
    }

    public function testCanFindFirstAndLastCall()
    {
        $callSpan = $this->programService->findFirstAndLastCall();

        $this->assertInstanceOf('stdClass', $callSpan);
    }

    public function testCanFindNdaByCallAndContact()
    {
        $call    = $this->programService->findEntityById('call', 1);
        $contact = $this->contactService->findEntityById('contact', 1);

        $nda = $this->programService->findNdaByCallAndContact($call, $contact);
        $this->assertNull($nda);
    }

    public function testCanUploadNDA()
    {

        $call    = $this->programService->findEntityById('call', 1);
        $contact = $this->contactService->findEntityById('contact', 1);

        $file = array(
            'name'     => 'This is an uploaded file',
            'type'     => 'application/pdf',
            'tmp_name' => __DIR__ . '/../../data/template_nda.pdf',
            'error'    => 0,
            'size'     => 145000,
        );

        $uploadedNda = $this->programService->uploadNda($file, $contact, $call);
        $this->assertInstanceOf("Program\\Entity\\NdaObject", $uploadedNda);
        $this->assertEquals($file['size'], $uploadedNda->getNda()->getSize());
        $this->assertNotNull($uploadedNda->getObject());
    }
}
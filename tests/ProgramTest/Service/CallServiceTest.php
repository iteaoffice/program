<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace ProgramTest\Service;

use Contact\Service\ContactService;
use Program\Service\CallService;
use Program\Service\ProgramService;
use ProgramTest\Bootstrap;

class CallServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var CallService;
     */
    protected $callService;
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
        $this->callService = new CallService();
        $this->callService->setServiceLocator($this->serviceManager);
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

    public function testCanFindNdaByCallAndContact()
    {
        $call = $this->callService->findEntityById('Call\Call', 1);
        $contact = $this->contactService->findEntityById('contact', 1);
        $nda = $this->callService->findNdaByCallAndContact($call, $contact);
        $this->assertNull($nda);
    }

    public function testCanUploadNDA()
    {
        $call = $this->callService->setCallId(1)->getCall();
        $contact = $this->contactService->setContactId(1)->getContact();
        $file = [
            'name'     => 'This is an uploaded file',
            'type'     => 'application/pdf',
            'tmp_name' => __DIR__ . '/../../data/template_nda.pdf',
            'error'    => 0,
            'size'     => 145000,
        ];
        $uploadedNda = $this->callService->uploadNda($file, $contact, $call);
        $this->assertInstanceOf("Program\\Entity\\NdaObject", $uploadedNda);
        $this->assertEquals($file['size'], $uploadedNda->getNda()->getSize());
        $this->assertNotNull($uploadedNda->getObject());
    }
}

<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace ProgramTest\Entity;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Program\Entity\Call\Call;
use ProgramTest\Bootstrap;

class CallTest extends \PHPUnit_Framework_TestCase
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
     * @var Call;
     */
    protected $call;
    /**
     * @var array
     */
    protected $callData;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');
        $program = $this->entityManager->find("Program\Entity\Program", 1);
        $this->callData = array(
            'call'    => 'ITEA2',
            'program' => $program
        );
        $this->call = new Call();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Program\Entity\Call\Call", $this->call);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $this->call);
        $this->assertNull($this->call->getCall(), 'The "Call" should be null');
        $id = 1;
        $this->call->setId($id);
        $this->assertEquals($id, $this->call->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($this->call->getArrayCopy()));
        $this->assertTrue(is_array($this->call->populate()));
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->call->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $this->call->call = 'test';
        $this->assertEquals('test', $this->call->call);
    }

    public function testCanHydrateEntity()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Call\Call'
        );
        $this->call = $hydrator->hydrate($this->callData, new Call());
        $dataArray = $hydrator->extract($this->call);
        $this->assertSame($this->callData['call'], $dataArray['call']);
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Call\Call'
        );
        $this->call = $hydrator->hydrate($this->callData, new Call());
        $this->entityManager->persist($this->call);
        $this->entityManager->flush();
        $this->assertInstanceOf('Program\Entity\Call\Call', $this->call);
        $this->assertNotNull($this->call->getId());
        $this->assertSame($this->callData['call'], $this->call->getCall());
    }
}

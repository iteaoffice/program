<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    CallTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace ProgramTest\Entity;

use Program\Entity\Call;

use ProgramTest\Bootstrap;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

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

        $this->assertInstanceOf("Program\Entity\Call", $this->call);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $this->call);

        $this->assertNull($this->call->getProgram(), 'The "Program" should be null');

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
            'Program\Entity\Program'
        );

        $this->call = $hydrator->hydrate($this->callData, new Call());

        $dataArray = $hydrator->extract($this->call);

        $this->assertSame($this->callData['call'], $dataArray['call']);
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Call'
        );

        $this->call = $hydrator->hydrate($this->callData, new Call());
        $this->entityManager->persist($this->call);
        $this->entityManager->flush();

        $this->assertInstanceOf('Program\Entity\Call', $this->call);
        $this->assertNotNull($this->call->getId());
        $this->assertSame($this->callData['call'], $this->call->getCall());
    }
}

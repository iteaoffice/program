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
        $this->entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    public function provider()
    {
        $programTest = new ProgramTest();

        $call = new Call();
        $call->setCall('TEST1');
        $call->setDoaRequirement(Call::DOA_REQUIREMENT_PER_PROGRAM);
        $call->setProgram($programTest->provider()[0][0]);

        return [
            [$call]
        ];
    }

    public function testCanCreateEntity()
    {
        $call = new Call();
        $this->assertInstanceOf("Program\Entity\Call\Call", $call);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $call);
        $this->assertNull($call->getCall(), 'The "Call" should be null');
        $id = 1;
        $call->setId($id);
        $this->assertEquals($id, $call->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($call->getArrayCopy()));
        $this->assertTrue(is_array($call->populate()));
    }

    public function testHasInputFilter()
    {
        $call = new Call();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $call->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $call = new Call();
        $call->call = 'test';
        $this->assertEquals('test', $call->call);
    }

    /**
     * @param Call $call
     *
     * @dataProvider provider
     */
    public function testCanSaveEntityInDatabase(Call $call)
    {
        $this->entityManager->persist($call);
        $this->entityManager->flush();
        $this->assertInstanceOf('Program\Entity\Call\Call', $call);
        $this->assertNotNull($call->getId());
    }
}

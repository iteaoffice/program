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

use Program\Entity\Program;
use ProgramTest\Bootstrap;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class ProgramTest extends \PHPUnit_Framework_TestCase
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
     * @var Program;
     */
    protected $program;
    /**
     * @var array
     */
    protected $programData;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->programData = array(
            'program' => 'ITEA2',
        );

        $this->program = new Program();
    }

    public function testCanCreateEntity()
    {

        $this->assertInstanceOf("Program\Entity\Program", $this->program);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $this->program);

        $this->assertNull($this->program->getProgram(), 'The "Program" should be null');

        $id = 1;
        $this->program->setId($id);

        $this->assertEquals($id, $this->program->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->program->getArrayCopy()));
        $this->assertTrue(is_array($this->program->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $this->program->program = 'test';
        $this->assertEquals('test', $this->program->program);
    }

    public function testCanHydrateEntity()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Program'
        );

        $this->program = $hydrator->hydrate($this->programData, new Program());

        $dataArray = $hydrator->extract($this->program);

        $this->assertSame($this->programData['program'], $dataArray['program']);
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->program->getInputFilter());
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Program'
        );

        $this->program = $hydrator->hydrate($this->programData, new Program());
        $this->entityManager->persist($this->program);
        $this->entityManager->flush();

        $this->assertInstanceOf('Program\Entity\Program', $this->program);
        $this->assertNotNull($this->program->getId());
        $this->assertSame($this->programData['program'], $this->program->getProgram());
    }
}

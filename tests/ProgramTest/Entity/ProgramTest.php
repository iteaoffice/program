<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace ProgramTest\Entity;

use Program\Entity\Program;
use ProgramTest\Bootstrap;

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
     * @return array
     */
    public function provider()
    {
        $program = new Program();
        $program->setProgram('PROGRAM1');

        return [
            [$program]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

    }

    public function testCanCreateEntity()
    {
        $program = new Program();
        $this->assertInstanceOf("Program\Entity\Program", $program);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $program);
        $this->assertNull($program->getProgram(), 'The "Program" should be null');
        $id = 1;
        $program->setId($id);
        $this->assertEquals($id, $program->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($program->getArrayCopy()));
        $this->assertTrue(is_array($program->populate()));
    }

    public function testMagicGettersAndSetters()
    {
        $program = new Program();
        $program->program = 'test';
        $this->assertEquals('test', $program->program);
    }

    public function testHasInputFilter()
    {
        $program = new Program();
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $program->getInputFilter());
    }

    /**
     * @param Program $program
     *
     * @dataProvider provider
     */
    public function testCanSaveEntityInDatabase(Program $program)
    {
        $this->entityManager->persist($program);
        $this->entityManager->flush();
        $this->assertInstanceOf('Program\Entity\Program', $program);
        $this->assertNotNull($program->getId());
    }
}

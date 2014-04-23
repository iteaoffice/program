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

use Program\Entity\Program;
use Program\Service\ProgramService;
use ProgramTest\Bootstrap;

class GeneralServiceTest extends \PHPUnit_Framework_TestCase
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
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->programService = new ProgramService();
        $this->programService->setServiceLocator($this->serviceManager);
    }

    public function testHasEntityManager()
    {
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->programService->getEntityManager());
    }

    public function testCanFindAll()
    {
        $entity   = 'program';
        $programs = $this->programService->findAll($entity);
        $this->assertNotNull($programs);
        $this->assertTrue(is_array($programs));
        foreach ($programs as $program) {
            $this->assertInstanceOf($this->programService->getFullEntityName($entity), $program);
        }
    }

    public function testCanCreateNewEntity()
    {
        $program    = $this->entityManager->find("Program\Entity\Program", 1);
        $newProgram = clone $program;
        $newProgram->setId(null);
        $newProgram->setProgram('This is a new entity');
        $newProgram = $this->programService->newEntity($newProgram);
        $this->assertInstanceOf("Program\Entity\Program", $newProgram);
        $this->assertNotEquals($newProgram->getId(), $program->getId());
    }

    public function testCanUpdateEntity()
    {
        $newProgramName = 'ITEA3';
        $program        = $this->entityManager->find("Program\Entity\Program", 1);
        $program->setProgram($newProgramName);
        $this->programService->updateEntity($program);

        $reloadProgram = $this->entityManager->find("Program\Entity\Program", 1);
        $this->assertInstanceOf("Program\Entity\Program", $reloadProgram);
        $this->assertEquals($reloadProgram->getProgram(), $newProgramName);
    }

    public function testCanRemoveEntity()
    {
        $program = $this->entityManager->find("Program\Entity\Program", 2);
        $this->programService->removeEntity($program);

        $reloadProgram = $this->entityManager->find("Program\Entity\Program", 2);
        $this->assertNull($reloadProgram);
    }

    public function testCanGetEntity()
    {
        $entity    = 'program';
        $getEntity = $this->programService->getEntity($entity);
        $this->assertTrue(is_object($getEntity));
        $this->assertInstanceOf('Program\Entity\Program', $getEntity);
    }

    public function testCanGetFullEntityName()
    {
        $entity     = 'program';
        $entityName = $this->programService->getFullEntityName($entity);
        $this->assertEquals('Program\Entity\Program', $entityName);

        $entity     = 'test-program';
        $entityName = $this->programService->getFullEntityName($entity);
        $this->assertEquals('Program\Entity\TestProgram', $entityName);
    }

    public function testCanFindEntity()
    {
        $entity   = 'program';
        $entityId = 1;
        $program  = $this->programService->findEntityById($entity, $entityId);
        $this->assertNotNull($program);
        $this->assertInstanceOf('Program\Entity\Program', $program);
        $this->assertEquals($program->getId(), $entityId);
    }

    public function testGetEntity()
    {
        $entity         = 'program';
        $fullEntity     = $this->programService->getEntity($entity);
        $fullEntityName = $this->programService->getFullEntityName($entity);
        $this->assertInstanceOf($fullEntityName, $fullEntity);
    }
}

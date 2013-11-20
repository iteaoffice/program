<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    RoadmapTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace ProgramTest\Entity;

use Program\Entity\Roadmap;

use ProgramTest\Bootstrap;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element\DateTime;

class RoadmapTest extends \PHPUnit_Framework_TestCase
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
     * @var Roadmap;
     */
    protected $roadmap;
    /**
     * @var array
     */
    protected $roadmapData;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->roadmapData = array(
            'roadmap'      => 2,
            'dateReleased' => new \DateTime(),
            'description'  => 'This is the first roadmap'
        );

        $this->roadmap = new Roadmap();
    }

    public function testCanCreateEntity()
    {

        $this->assertInstanceOf("Program\Entity\Roadmap", $this->roadmap);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $this->roadmap);

        $this->assertNull($this->roadmap->getRoadmap(), 'The "Roadmap" should be null');

        $id = 1;
        $this->roadmap->setId($id);

        $this->assertEquals($id, $this->roadmap->getId(), 'The "Id" should be the same as the setter');

        $this->assertTrue(is_array($this->roadmap->getArrayCopy()));
        $this->assertTrue(is_array($this->roadmap->populate()));
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->roadmap->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $this->roadmap->roadmap = 'test';
        $this->assertEquals('test', $this->roadmap->roadmap);
    }

    public function testCanHydrateEntity()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Roadmap'
        );

        $this->roadmap = $hydrator->hydrate($this->roadmapData, new Roadmap());

        $dataArray = $hydrator->extract($this->roadmap);

        $this->assertSame($this->roadmapData['roadmap'], $dataArray['roadmap']);
        $this->assertSame($this->roadmapData['description'], $dataArray['description']);
        $this->assertSame($this->roadmapData['dateReleased'], $dataArray['dateReleased']);
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Roadmap'
        );

        $this->roadmap = $hydrator->hydrate($this->roadmapData, new Roadmap());
        $this->entityManager->persist($this->roadmap);
        $this->entityManager->flush();

        $this->assertInstanceOf('Program\Entity\Roadmap', $this->roadmap);
        $this->assertNotNull($this->roadmap->getId());
        $this->assertSame($this->roadmapData['roadmap'], $this->roadmap->getRoadmap());
        $this->assertSame($this->roadmapData['description'], $this->roadmap->getDescription());
        $this->assertSame($this->roadmapData['dateReleased'], $this->roadmap->getDateReleased());
    }
}

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
use Program\Entity\Domain;
use ProgramTest\Bootstrap;

class DomainTest extends \PHPUnit_Framework_TestCase
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
     * @var Domain;
     */
    protected $domain;
    /**
     * @var array
     */
    protected $domainData;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $roadmap = $this->entityManager->find("Program\Entity\Roadmap", 1);
        $this->domainData = [
            'domain'      => 'ITEA2',
            'roadmap'     => $roadmap,
            'description' => 'This is a second description',
            'color'       => '#abcdef',
            'mainId'      => '2'
        ];
        $this->domain = new Domain();
    }

    public function testCanCreateEntity()
    {
        $this->assertInstanceOf("Program\Entity\Domain", $this->domain);
        $this->assertInstanceOf("Program\Entity\EntityInterface", $this->domain);
        $this->assertNull($this->domain->getDomain(), 'The "Program" should be null');
        $id = 1;
        $this->domain->setId($id);
        $this->assertEquals($id, $this->domain->getId(), 'The "Id" should be the same as the setter');
        $this->assertTrue(is_array($this->domain->getArrayCopy()));
        $this->assertTrue(is_array($this->domain->populate()));
    }

    public function testHasInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->domain->getInputFilter());
    }

    public function testMagicGettersAndSetters()
    {
        $this->domain->domain = 'test';
        $this->assertEquals('test', $this->domain->domain);
    }

    public function testCanHydrateEntity()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Domain'
        );
        $this->domain = $hydrator->hydrate($this->domainData, new Domain());
        $dataArray = $hydrator->extract($this->domain);
        $this->assertSame($this->domainData['domain'], $dataArray['domain']);
        $this->assertSame($this->domainData['roadmap']->getId(), $dataArray['roadmap']->getId());
        $this->assertSame($this->domainData['description'], $dataArray['description']);
        $this->assertSame($this->domainData['color'], $dataArray['color']);
        $this->assertSame($this->domainData['mainId'], $dataArray['mainId']);
    }

    public function testCanSaveEntityInDatabase()
    {
        $hydrator = new DoctrineObject(
            $this->entityManager,
            'Program\Entity\Domain'
        );
        $this->domain = $hydrator->hydrate($this->domainData, new Domain());
        $this->entityManager->persist($this->domain);
        $this->entityManager->flush();
        $this->assertInstanceOf('Program\Entity\Domain', $this->domain);
        $this->assertNotNull($this->domain->getId());
        $this->assertSame($this->domainData['domain'], $this->domain->getDomain());
        $this->assertSame($this->domainData['roadmap']->getId(), $this->domain->getRoadmap()->getId());
        $this->assertSame($this->domainData['description'], $this->domain->getDescription());
        $this->assertSame($this->domainData['color'], $this->domain->getColor());
        $this->assertSame($this->domainData['mainId'], $this->domain->getMainId());
    }
}

<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace ProgramTest\Builder;

use Program\Builder\Nda;
use ProgramTest\Bootstrap;

class NdaTest extends \PHPUnit_Framework_TestCase
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
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        \Core_Config::setValue('DEFAULT_DATABASE', 'itea_office_development');
        \Core_Config::setValue('STYLE', 'itea');
    }

    public function testCanConstruct()
    {
        $contact    = $this->entityManager->find('Contact\Entity\Contact', 1);
        $call       = $this->entityManager->find("Program\Entity\Call\Call", 1);
        $builderNda = new Nda($contact, $call);
        $this->assertInstanceOf('Program\\Builder\\Nda', $builderNda);
    }

    /**
     * @covers Builder_Nda_Pdf::generate()
     * @covers Builder_PdfXml
     */
    public function testCanRender()
    {
        $contact    = $this->entityManager->find('Contact\Entity\Contact', 1);
        $call       = $this->entityManager->find("Program\Entity\Call\Call", 1);
        $builderNda = new Nda($contact, $call);
        $this->assertContains('PDF-1.4', $builderNda->getPdf());
    }
}

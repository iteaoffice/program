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

use Program\Service\FormService;
use ProgramTest\Bootstrap;
use InvalidArgumentException;

class FormServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var FormService;
     */
    protected $formService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->entityManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $this->formService = new FormService();
        $this->formService->setServiceLocator($this->serviceManager);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCannotCreateEmptyForm()
    {
        $this->assertInstanceOf('Zend\Form\Form', $this->formService->getForm());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCanGetFormWithEmptyEntity()
    {
        $entity = 'program';
        $this->assertInstanceOf('Zend\Form\Form', $this->formService->getForm(null, $entity));
    }

    public function testCanCreateFormFormNewEntity()
    {
        $entity = 'program';
        $this->assertInstanceOf('Zend\Form\Form', $this->formService->getForm($entity));
    }

    public function testCanCreateFormAndBindEntity()
    {
        $program  = $this->entityManager->find("Program\Entity\Program", 1);
        $nodeForm = $this->formService->getForm(null, $program, true);

        $this->assertInstanceOf('Zend\Form\Form', $nodeForm);
    }

    public function testCanPrepareForm()
    {
        $entity = 'program';
        $this->assertInstanceOf('Zend\Form\Form', $this->formService->prepare($entity));
    }

    public function testCanCreateEntityForms()
    {
        $entities = array(
            'program',
        );

        foreach ($entities as $entity) {
            $this->assertInstanceOf('Zend\Form\Form', $this->formService->getForm($entity));
        }
    }
}

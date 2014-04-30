<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace ProgramTest\Form;

use Program\Entity\Funder;
use Program\Form\ObjectFieldset;
use ProgramTest\Bootstrap;
use Program\Entity\Program;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class ObjectFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $entityManager;
    /**
     * @var ObjectFieldset;
     */
    protected $objectFieldset;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    public function testCanCreateObjectFieldsetFormProgram()
    {
        $objectFieldset = new ObjectFieldset(
            $this->serviceManager->get('doctrine.entitymanager.orm_default'),
            new Program()
        );

        $this->assertInstanceOf("Zend\Form\Fieldset", $objectFieldset);
    }

    public function testCanCreateObjectFieldsetFormFunder()
    {
        $objectFieldset = new ObjectFieldset(
            $this->serviceManager->get('doctrine.entitymanager.orm_default'),
            new Funder()
        );

        $this->assertInstanceOf("Zend\Form\Fieldset", $objectFieldset);
    }
}

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

use Program\Form\CreateObject;
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
class CreateObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    public function testCanCanCreateFormWithObjectSpecificTemplate()
    {
        $createObject = new CreateObject($this->serviceManager, new Program());
        $this->assertInstanceOf("Zend\Form\Form", $createObject);
    }
}

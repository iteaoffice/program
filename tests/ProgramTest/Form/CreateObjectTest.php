<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
 */
namespace ProgramTest\Form;

use Program\Entity\Program;
use Program\Form\CreateObject;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
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

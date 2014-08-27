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

use Program\Form\UploadDoa;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class UploadDoaTest extends \PHPUnit_Framework_TestCase
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

    public function testCanCreateDoaUploadForm()
    {
        $this->assertInstanceOf("Zend\Form\Form", new UploadDoa());
        $this->assertInstanceOf("Zend\InputFilter\InputFilterProviderInterface", new UploadDoa());
    }

    public function testUploadDoaFormHasAllElements()
    {
        $uploadDoaForm = new UploadDoa();
        $this->assertTrue($uploadDoaForm->has('file'));
        $this->assertTrue($uploadDoaForm->has('submit'));
        $this->assertTrue($uploadDoaForm->has('cancel'));
    }

    public function testUploadDoaFormHasCorrectInputFilter()
    {
        $uploadDoaForm = new UploadDoa();
        $inputFilter = $uploadDoaForm->getInputFilterSpecification();
        $this->assertTrue(is_array($inputFilter));
        $this->assertArrayHasKey('file', $inputFilter);
    }
}

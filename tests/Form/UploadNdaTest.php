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

use Program\Form\UploadNda;
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
class UploadNdaTest extends \PHPUnit_Framework_TestCase
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

    public function testCanCreateNdaUploadForm()
    {
        $this->assertInstanceOf("Zend\Form\Form", new UploadNda());
        $this->assertInstanceOf("Zend\InputFilter\InputFilterProviderInterface", new UploadNda());
    }

    public function testUploadNdaFormHasAllElements()
    {
        $uploadNdaForm = new UploadNda();
        $this->assertTrue($uploadNdaForm->has('file'));
        $this->assertTrue($uploadNdaForm->has('submit'));
        $this->assertTrue($uploadNdaForm->has('cancel'));
    }

    public function testUploadNdaFormHasCorrectInputFilter()
    {
        $uploadNdaForm = new UploadNda();
        $inputFilter   = $uploadNdaForm->getInputFilterSpecification();
        $this->assertTrue(is_array($inputFilter));
        $this->assertArrayHasKey('file', $inputFilter);
    }
}

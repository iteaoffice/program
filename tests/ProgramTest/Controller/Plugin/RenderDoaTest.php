<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
 */
namespace ProgramTest\Controller\Plugin;

use Contact\Entity\Contact;
use Program\Controller\Plugin\RenderDoa;
use Program\Entity\Doa;
use Program\Entity\Program;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
 */
class RenderDoaTest extends \PHPUnit_Framework_TestCase
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

    public function testCanRenderDoa()
    {
        /**
         * Bootstrap the application to have the other information available
         */
//        $application = $this->serviceManager->get('application');
//        $application->bootstrap();
        $renderDoa = new RenderDoa();
        $renderDoa->setServiceLocator($this->serviceManager);
        $contact = new Contact();
        $contact->setFirstName('Johan');
        $contact->setLastName('van der Heide');
        $program = new Program();
        $program->setProgram('testProgram');
        $doa = new Doa();
        $doa->setContact($contact);
        $doa->setProgram($program);
        $pdf = $renderDoa->renderForDoa($doa);
        $this->assertInstanceOf("Program\Controller\Plugin\ProgramPdf", $pdf);
        $this->assertTrue(strlen($pdf->getPDFData()) > 0);
    }
}

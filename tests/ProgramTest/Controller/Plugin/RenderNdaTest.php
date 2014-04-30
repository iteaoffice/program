<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace ProgramTest\Controller\Plugin;

use Contact\Entity\Contact;
use Program\Controller\Plugin\RenderNda;
use Program\Entity\Nda;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class RenderNdaTest extends \PHPUnit_Framework_TestCase
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

    public function testCanRenderNda()
    {

        /**
         * Bootstrap the application to have the other information available
         */
//        $application = $this->serviceManager->get('application');
//        $application->bootstrap();

        $renderNda = new RenderNda();
        $renderNda->setServiceLocator($this->serviceManager);

        $contact = new Contact();
        $contact->setFirstName('Johan');
        $contact->setLastName('van der Heide');

        $nda = new Nda();
        $nda->setContact($contact);

        $pdf = $renderNda->render($nda);

        $this->assertInstanceOf("Program\Controller\Plugin\ProgramPdf", $pdf);

        $this->assertTrue(strlen($pdf->getPDFData()) > 0);
    }

    public function testCanRenderCallNda()
    {
        $renderNda = new RenderNda();
        $renderNda->setServiceLocator($this->serviceManager);

        $contact = new Contact();
        $contact->setFirstName('Johan');
        $contact->setLastName('van der Heide');

        $nda = new Nda();
        $nda->setContact($contact);

        $pdf = $renderNda->renderCall($nda);

        $this->assertInstanceOf("Program\Controller\Plugin\ProgramPdf", $pdf);

        $this->assertTrue(strlen($pdf->getPDFData()) > 0);
    }
}

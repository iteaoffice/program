<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
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
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
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
        $pdf = $renderNda->renderForCall($nda);
        $this->assertInstanceOf("Program\Controller\Plugin\ProgramPdf", $pdf);
        $this->assertTrue(strlen($pdf->getPDFData()) > 0);
    }
}

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\View\Helper;

use BjyAuthorize\Service\Authorize;
use Contact\Entity\Contact;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\Program;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class NdaLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    /**
     * @var NdaLink;
     */
    protected $ndaLink;
    /**
     * @var Nda
     */
    protected $nda;
    /**
     * @var Authorize
     */
    protected $authorizeService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->nda = new Nda();
        $this->nda->setId(1);

        $contact = new Contact();
        $contact->setId(1234);
        $this->nda->setContact($contact);

        $program = new Program();
        $program->setId(1);
        $program->setProgram('Program');

        $call = new Call();
        $call->setId(1);
        $call->setCall("Call");
        $call->setProgram($program);
        $this->nda->setCall($call);

        $this->authorizeService = $this->serviceManager->get('BjyAuthorize\Service\Authorize');

        if (!$this->authorizeService->getAcl()->hasResource($this->nda)) {
            $this->authorizeService->getAcl()->addResource($this->nda);
            $this->authorizeService->getAcl()->allow(array(), $this->nda, array());
        }

        /**
         * Add the resource on the fly
         */
        if (!$this->authorizeService->getAcl()->hasResource(new Nda())) {
            $this->authorizeService->getAcl()->addResource(new Nda());
        }
        $this->authorizeService->getAcl()->allow(array(), new Nda(), array());

        $this->ndaLink = $this->serviceManager->get('viewhelpermanager')->get('ndaLink');

        /**
         * Bootstrap the application to have the other information available
         */
        $application = $this->serviceManager->get('application');
        $application->bootstrap();
    }

    public function testCanCreateNdaLink()
    {
        $this->assertInstanceOf("Program\View\Helper\NdaLink", $this->ndaLink);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCannotViewEmptyNda()
    {
        $this->ndaLink->__invoke(null, 'view');
    }

    public function testGetAccessDenied()
    {
        $this->authorizeService->getAcl()->deny(array(), $this->nda, array());
        $this->assertNotContains('<a href', $this->ndaLink->__invoke($this->nda, 'download'));
        $this->authorizeService->getAcl()->allow(array(), $this->nda, array());
    }

    public function testCanCreateDifferentNdaLinks()
    {
        $this->assertContains(
            '<a href',
            $this->ndaLink->__invoke($this->nda, 'upload')
        );
        $this->assertContains(
            '<a href',
            $this->ndaLink->__invoke($this->nda, 'render')
        );
        $this->assertContains(
            '<a href',
            $this->ndaLink->__invoke($this->nda, 'replace')
        );
        $this->assertContains(
            '<a href',
            $this->ndaLink->__invoke($this->nda, 'download')
        );
    }

    public function testThrowsExceptionForWrongInviteLink()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->ndaLink->__invoke($this->nda, 'blaat');
    }
}

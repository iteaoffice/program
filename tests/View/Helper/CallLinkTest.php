<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
 */
namespace ProgramTest\View\Helper;

use BjyAuthorize\Service\Authorize;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Program\View\Helper\CallLink;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
 */
class CallLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    /**
     * @var CallLink;
     */
    protected $callLink;
    /**
     * @var Call
     */
    protected $call;
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
        $this->call = new Call();
        $this->call->setId(1);
        $this->call->setCall('This is the call');
        $program = new Program();
        $program->setProgram('This is the program');
        $this->call->setProgram($program);
        $this->authorizeService = $this->serviceManager->get('BjyAuthorize\Service\Authorize');
        if (!$this->authorizeService->getAcl()->hasResource($this->call)) {
            $this->authorizeService->getAcl()->addResource($this->call);
            $this->authorizeService->getAcl()->allow([], $this->call, []);
        }
        /**
         * Add the resource on the fly
         */
        if (!$this->authorizeService->getAcl()->hasResource(new Call())) {
            $this->authorizeService->getAcl()->addResource(new Call());
        }
        $this->authorizeService->getAcl()->allow([], new Call(), []);
        $this->callLink = $this->serviceManager->get('viewhelpermanager')->get('calllink');
        /**
         * Bootstrap the application to have the other information available
         */
        $application = $this->serviceManager->get('application');
        $application->bootstrap();
    }

    public function testCanCreateCallLink()
    {
        $this->assertInstanceOf("Program\View\Helper\CallLink", $this->callLink);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCannotViewEmptyCall()
    {
        $this->callLink->__invoke(null, 'view-list');
    }

    public function testCanCreateListCallLink()
    {
        $this->assertContains('<a href', $this->callLink->__invoke($this->call, 'view-list', 'text'));
    }

    public function testCanCreateDifferentCallLinks()
    {
        $this->assertContains(
            '<a href',
            $this->callLink->__invoke($this->call, 'view-list')
        );
        $this->assertContains(
            '<a href',
            $this->callLink->__invoke($this->call, 'view-list')
        );
    }

    public function testThrowsExceptionForWrongCallLink()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->callLink->__invoke($this->call, 'blaat');
    }

    public function testCanCreateSocialLink()
    {
        $this->assertNotContains('<a href', $this->callLink->__invoke($this->call, 'view-list', 'social'));
    }

    public function testCanCreateLinkWithButton()
    {
        $this->assertContains('btn', $this->callLink->__invoke($this->call, 'view-list', 'button'));
    }

    public function testCanCreateLinkWithName()
    {
        $this->assertNotContains('btn', $this->callLink->__invoke($this->call, 'view-list', 'name'));
    }

    public function testCanCreateLinkWithNumber()
    {
        $this->assertNotContains('btn', $this->callLink->__invoke($this->call, 'view-list', 'name'));
    }
}

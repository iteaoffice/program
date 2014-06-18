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
namespace ProgramTest\View\Helper;

use BjyAuthorize\Service\Authorize;
use Program\Entity\Program;
use Program\View\Helper\ProgramLink;
use ProgramTest\Bootstrap;
use Zend\Mvc\Router\Http\RouteMatch;

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
class ProgramLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    /**
     * @var ProgramLink;
     */
    protected $programLink;
    /**
     * @var Program
     */
    protected $program;
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
        $this->program        = new Program();
        $this->program->setId(1);
        $this->program->setProgram('This is the program');
        $this->authorizeService = $this->serviceManager->get('BjyAuthorize\Service\Authorize');
        if (!$this->authorizeService->getAcl()->hasResource($this->program)) {
            $this->authorizeService->getAcl()->addResource($this->program);
            $this->authorizeService->getAcl()->allow([], $this->program, []);
        }
        /**
         * Add the resource on the fly
         */
        if (!$this->authorizeService->getAcl()->hasResource(new Program())) {
            $this->authorizeService->getAcl()->addResource(new Program());
        }
        $this->authorizeService->getAcl()->allow([], new Program(), []);
        $this->programLink = $this->serviceManager->get('viewhelpermanager')->get('programlink');
        $routeMatch = new RouteMatch(
            array(
                'program' => 1,
            )
        );
        $routeMatch->setMatchedRouteName('route-program_entity_program');
        $this->programLink->setRouteMatch($routeMatch);
        /**
         * Bootstrap the application to have the other information available
         */
        $application = $this->serviceManager->get('application');
        $application->bootstrap();
    }

    public function testCanCreateProgramLink()
    {
        $this->assertInstanceOf("Program\View\Helper\ProgramLink", $this->programLink);
    }

    public function testCanCreateListProjectLink()
    {
        $this->assertContains('<a href', $this->programLink->__invoke(null, 'view-list', 'text'));
    }

    public function testCanCreateDifferentProgramLinks()
    {
        $this->assertContains(
            '<a href',
            $this->programLink->__invoke($this->program, 'view-list')
        );
        $this->assertContains(
            '<a href',
            $this->programLink->__invoke($this->program, 'view-list')
        );
    }

    public function testThrowsExceptionForWrongProgramLink()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->programLink->__invoke($this->program, 'blaat');
    }

    public function testCanCreateSocialLink()
    {
        $this->assertNotContains('<a href', $this->programLink->__invoke($this->program, 'view-list', 'social'));
    }

    public function testCanCreateLinkWithButton()
    {
        $this->assertContains('btn', $this->programLink->__invoke($this->program, 'view-list', 'button'));
    }

    public function testCanCreateLinkWithName()
    {
        $this->assertNotContains('btn', $this->programLink->__invoke($this->program, 'view-list', 'name'));
    }

    public function testCanCreateLinkWithNumber()
    {
        $this->assertNotContains('btn', $this->programLink->__invoke($this->program, 'view-list', 'name'));
    }
}

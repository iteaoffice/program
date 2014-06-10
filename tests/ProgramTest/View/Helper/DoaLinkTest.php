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
use Organisation\Entity\Organisation;
use Program\Entity\Doa;
use Program\Entity\Program;
use Program\View\Helper\DoaLink;
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
class DoaLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    /**
     * @var DoaLink;
     */
    protected $doaLink;
    /**
     * @var Doa
     */
    protected $doa;
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

        $this->doa = new Doa();
        $this->doa->setId(1);

        $program = new Program();
        $program->setId(1);
        $program->setProgram('Program');
        $this->doa->setProgram($program);

        $organisation = new Organisation();
        $organisation->setId(1);
        $organisation->setOrganisation("Organisation");
        $this->doa->setOrganisation($organisation);

        $this->authorizeService = $this->serviceManager->get('BjyAuthorize\Service\Authorize');

        if (!$this->authorizeService->getAcl()->hasResource($this->doa)) {
            $this->authorizeService->getAcl()->addResource($this->doa);
            $this->authorizeService->getAcl()->allow([], $this->doa, []);
        }

        /**
         * Add the resource on the fly
         */
        if (!$this->authorizeService->getAcl()->hasResource(new Doa())) {
            $this->authorizeService->getAcl()->addResource(new Doa());
        }
        $this->authorizeService->getAcl()->allow([], new Doa(), []);

        $this->doaLink = $this->serviceManager->get('viewhelpermanager')->get('programDoaLink');

        /**
         * Bootstrap the application to have the other information available
         */
        $application = $this->serviceManager->get('application');
        $application->bootstrap();
    }

    public function testCanCreateDoaLink()
    {
        $this->assertInstanceOf("Program\View\Helper\DoaLink", $this->doaLink);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCannotViewEmptyDoa()
    {
        $this->doaLink->__invoke(null, 'view');
    }

    public function canRenderEmptyNda()
    {
        $this->doaLink->__invoke(null, 'upload');
        $this->assertInstanceOf('Program\Entity\Doa', $this->doaLink->getDoa());
    }

    public function testGetAccessDenied()
    {
        $this->authorizeService->getAcl()->deny([], $this->doa, []);
        $this->assertNotContains('<a href', $this->doaLink->__invoke($this->doa, 'view-community'));
        $this->authorizeService->getAcl()->allow([], $this->doa, []);
    }

    public function testCanCreateDifferentDoaLinks()
    {
        $this->assertContains(
            '<a href',
            $this->doaLink->__invoke($this->doa, 'upload')
        );
        $this->assertContains(
            '<a href',
            $this->doaLink->__invoke($this->doa, 'render')
        );
        $this->assertContains(
            '<a href',
            $this->doaLink->__invoke($this->doa, 'replace')
        );
        $this->assertContains(
            '<a href',
            $this->doaLink->__invoke($this->doa, 'download')
        );
    }

    public function testThrowsExceptionForWrongInviteLink()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->doaLink->__invoke($this->doa, 'blaat');
    }
}

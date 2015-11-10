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
use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\Program;
use Program\View\Helper\NdaLink;
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
        $this->nda->setCall(new ArrayCollection([$call]));

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

    public function canRenderEmptyNda()
    {
        $this->ndaLink->__invoke(null, 'upload');
        $this->assertInstanceOf('Program\Entity\Nda', $this->ndaLink->getNda());
    }

    public function testGetAccessDenied()
    {
        $authorizeServiceMock = $this->getMockBuilder('BjyAuthorize\View\Helper\IsAllowed')
            ->disableOriginalConstructor()
            ->getMock();
        $authorizeServiceMock->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue(false));
        $viewHelperManager = $this->serviceManager->get('viewhelpermanager');
        $viewHelperManager->setService('isAllowed', $authorizeServiceMock);
        $this->assertNotContains('<a href', $this->ndaLink->__invoke($this->nda, 'download'));

    }

    public function testCanCreateDifferentNdaLinks()
    {
        $authorizeServiceMock = $this->getMockBuilder('BjyAuthorize\View\Helper\IsAllowed')
            ->disableOriginalConstructor()
            ->getMock();
        $authorizeServiceMock->expects($this->any())
            ->method('__invoke')
            ->will($this->returnValue(true));
        $viewHelperManager = $this->serviceManager->get('viewhelpermanager');
        $viewHelperManager->setService('isAllowed', $authorizeServiceMock);

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

    public function testIncorrectShowReturnEmtpyString()
    {
        $this->assertEquals('', $this->ndaLink->__invoke($this->nda, 'blaat'));
    }
}

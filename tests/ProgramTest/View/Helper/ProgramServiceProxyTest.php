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
class ProgramServiceProxyTest extends \PHPUnit_Framework_TestCase
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

    public function testCanCreateProgramLink()
    {
        $programServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('programServiceProxy');

        $this->assertInstanceOf("Program\View\Helper\ProgramServiceProxy", $programServiceProxy);
    }

    public function testHasServiceManager()
    {
        $programServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('programServiceProxy');

        $this->assertInstanceOf(
            "Zend\ServiceManager\ServiceLocatorInterface",
            $programServiceProxy->getServiceLocator()
        );
    }

    public function testCanInvokeMethod()
    {
        $programServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('programServiceProxy');

        $program = new Program();

        $this->assertInstanceOf("Program\Service\ProgramService", $programServiceProxy->__invoke($program));
    }
}

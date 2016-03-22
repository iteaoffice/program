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

use Program\Entity\Call\Call;
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
class CallServiceProxyTest extends \PHPUnit_Framework_TestCase
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

    public function testCanCreateCallLink()
    {
        $callServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('callServiceProxy');
        $this->assertInstanceOf("Program\View\Helper\CallServiceProxy", $callServiceProxy);
    }

    public function testHasServiceManager()
    {
        $callServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('callServiceProxy');
        $this->assertInstanceOf(
            "Zend\ServiceManager\ServiceLocatorInterface",
            $callServiceProxy->getServiceLocator()
        );
    }

    public function testCanInvokeMethod()
    {
        $callServiceProxy = $this->serviceManager->get('viewhelpermanager')->get('callServiceProxy');
        $call = new Call();
        $this->assertInstanceOf("Program\Service\CallService", $callServiceProxy->__invoke($call));
    }
}
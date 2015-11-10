<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    Option
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
 */
namespace ProgramTest\Options;

use Program\Options\ModuleOptions;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    Option
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
 */
class ModuleOptionTest extends \PHPUnit_Framework_TestCase
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

    public function testCanCreateModuleOptions()
    {
        $moduleOptions = new ModuleOptions();
        $this->assertInstanceOf("Program\Options\ModuleOptions", $moduleOptions);
        $this->assertInstanceOf("Program\Options\ProgramOptionsInterface", $moduleOptions);
    }

    public function testModuleOptionsHasCorrectSetters()
    {
        $moduleOptions = new ModuleOptions();
        $this->assertEmpty($moduleOptions->getNdaTemplate());
    }
}

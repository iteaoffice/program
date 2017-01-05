<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   ProgramTest
 * @package    View
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 * @link       https://itea3.org
 */
namespace ProgramTest\Form;

use Program\Form\FilterCreateObject;
use ProgramTest\Bootstrap;

/**
 * Create a link to an project
 *
 * @category   ProgramTest
 * @package    View
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 * @link       https://itea3.org
 */
class FilterCreateObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    public function testCanCreateFilterCreateObject()
    {
        $this->assertInstanceOf("Zend\InputFilter\InputFilter", new FilterCreateObject());
    }
}

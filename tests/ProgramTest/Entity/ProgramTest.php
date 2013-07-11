<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 ITEA
 */
namespace ProgramTest\Entity;

use Program\Entity\Program;

class ProjectTest extends \PHPUnit_Framework_TestCase
{

    public function testCanCreateEntity()
    {
        $program = new Program();
        $this->assertInstanceOf("Program\Entity\Program", $program);
    }
}

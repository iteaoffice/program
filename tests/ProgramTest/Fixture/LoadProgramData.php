<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProgramTest
 * @package     Fixture
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace ProgramTest\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProgramData implements FixtureInterface
{
    /**
     * Load the Program
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $program = new \Program\Entity\Program();
        $program->setProgram('ITEA1');
        $manager->persist($program);
        $manager->flush();
    }
}

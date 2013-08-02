<?php
namespace ProgramTest\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

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

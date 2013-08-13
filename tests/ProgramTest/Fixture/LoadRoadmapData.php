<?php
namespace ProgramTest\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use ZendTest\View\_stubs\HelperDir2\Datetime;

class LoadRoadmapData implements FixtureInterface
{
    /**
     * Load the Program
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roadmap = new \Program\Entity\Roadmap();
        $roadmap->setRoadmap('11');
        $roadmap->setDateReleased(new \Datetime());
        $roadmap->setDescription('This is the description');

        $manager->persist($roadmap);
        $manager->flush();
    }
}

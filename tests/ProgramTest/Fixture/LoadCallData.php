<?php
namespace ProgramTest\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCallData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load the Gender
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $program = $manager->find("Program\Entity\Program", 1);

        $call = new \Program\Entity\Call();
        $call->setProgram($program);
        $call->setCall('1');
        $call->setPoOpenDate(new \DateTime());
        $call->setPoCloseDate(new \DateTime());
        $call->setFppOpenDate(new \DateTime());
        $call->setFppCloseDate(new \DateTime());

        $manager->persist($call);
        $manager->flush();
    }

    /**
     * fixture classes fixture is dependent on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'ProgramTest\Fixture\LoadProgramData',
        ); //
    }
}
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
        $call = new \Program\Entity\Call\Call();
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
        return [
            'ProgramTest\Fixture\LoadProgramData',
        ]; //
    }
}

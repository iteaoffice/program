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

class LoadDomainData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load the Gender
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roadmap = $manager->find("Program\Entity\Roadmap", 1);
        $domain = new \Program\Entity\Domain();
        $domain->setDomain('This is the domain');
        $domain->setRoadmap($roadmap);
        $domain->setDescription('This is the description');
        $domain->setColor('#efefef');
        $domain->setMainId(1);
        $manager->persist($domain);
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
            'ProgramTest\Fixture\LoadRoadmapData',
        ); //
    }
}

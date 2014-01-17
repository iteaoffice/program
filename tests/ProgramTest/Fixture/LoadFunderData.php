<?php
namespace ProgramTest\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFunderData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load the Gender
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $country = $manager->find("General\Entity\Country", 1);
        $contact = $manager->find("Contact\Entity\Contact", 1);

        $funder = new \Program\Entity\Funder();
        $funder->setContact($contact);
        $funder->setCountry($country);
        $funder->setInfoOffice('This is the info of the office');
        $funder->setInfoPublic('This is the info of the public');
        $funder->setShowOnWebsite(\Program\Entity\Funder::SHOW_ON_WEBSITE);

        $manager->persist($funder);
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
            'GeneralTest\Fixture\LoadCountryData',
            'ContactTest\Fixture\LoadContactData',
        ); //
    }
}

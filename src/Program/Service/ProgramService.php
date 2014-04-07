<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Service;

use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Program;

use Program\Entity\Funder;

/**
 * ProgramService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class ProgramService extends ServiceAbstract
{
    /**
     * @param Country $country
     *
     * @return Funder[]
     */
    public function findFunderByCountry(Country $country)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('funder'))->findBy(
            array('country' => $country)
        );
    }

    /**
     * @param Program      $program
     * @param Organisation $organisation
     *
     * @return null|\Program\Entity\Doa
     */
    public function findProgramDoaByProgramAndOrganisation(Program $program, Organisation $organisation)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Doa'))->findOneBy(
            array(
                'program'      => $program,
                'organisation' => $organisation
            )
        );
    }
}

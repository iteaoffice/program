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

use Affiliation\Service\AffiliationService;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Funder;
use Program\Entity\Program;

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
     * @var Program
     */
    protected $program;

    /**
     * @param $id
     *
     * @return $this
     */
    public function setProgramId($id)
    {
        $this->setProgram($this->findEntityById('Program', $id));

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->program) || is_null($this->program->getId());
    }

    /**
     * @param Country $country
     *
     * @return Funder[]
     */
    public function findFunderByCountry(Country $country)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('funder'))->findBy(
            ['country' => $country]
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
            [
                'program'      => $program,
                'organisation' => $organisation,
            ]
        );
    }

    /**
     * @return Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param $program
     *
     * @return $this
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @param $which
     *
     * @return AffiliationService[]
     */
    public function getAffiliation($which = AffiliationService::WHICH_ONLY_ACTIVE)
    {
        return $this->getAffiliationService()->findAffiliationByProjectAndWhich($this->project, $which);
    }
}

<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Service;

use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Doa;
use Program\Entity\Funder;
use Program\Entity\Program;

/**
 * ProgramService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 */
class ProgramService extends ServiceAbstract
{

    /**
     * @param $id
     *
     * @return null|Program
     */
    public function findProgramById($id)
    {
        return $this->getEntityManager()->getRepository(Program::class)->find($id);
    }

    /**
     * @param string $name
     *
     * @return null|Program
     */
    public function findProgramByName($name)
    {
        return $this->getEntityManager()->getRepository(Program::class)->findOneBy(['program' => $name]);
    }

    /**
     * Find the last open program and check which versionType is active.
     *
     * @return Program
     */
    public function findLastProgram()
    {
        return $this->getEntityManager()->getRepository(Program::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @param Program $program
     *
     * @return \stdClass
     */
    public function findMinAndMaxYearInProgram(Program $program)
    {
        /** @var \Program\Repository\Program $repository */
        $repository = $this->getEntityManager()->getRepository(Program::class);

        $yearSpanResult    = $repository->findMinAndMaxYearInProgram($program);
        $yearSpan          = new \stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    /**
     * @param Country $country
     *
     * @return Funder[]
     */
    public function findFunderByCountry(Country $country)
    {
        return $this->getEntityManager()->getRepository(Funder::class)
            ->findBy(['country' => $country], ['position' => 'ASC']);
    }

    /**
     * @param Program      $program
     * @param Organisation $organisation
     *
     * @return null|Doa
     */
    public function findProgramDoaByProgramAndOrganisation(Program $program, Organisation $organisation)
    {
        return $this->getEntityManager()->getRepository(Doa::class)->findOneBy(
            [
                'program'      => $program,
                'organisation' => $organisation,
            ]
        );
    }
}

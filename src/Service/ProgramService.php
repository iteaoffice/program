<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Service;

use General\Entity\Country;
use Organisation\Entity\Organisation;
use Program\Entity\Doa;
use Program\Entity\Funder;
use Program\Entity\Program;

/**
 * Class ProgramService
 * @package Program\Service
 */
class ProgramService extends ServiceAbstract
{

    /**
     * @param $id
     *
     * @return null|Program
     */
    public function findProgramById($id): ?Program
    {
        return $this->getEntityManager()->getRepository(Program::class)->find($id);
    }

    /**
     * @param string $name
     *
     * @return null|Program
     */
    public function findProgramByName($name): ?Program
    {
        return $this->getEntityManager()->getRepository(Program::class)->findOneBy(['program' => $name]);
    }

    /**
     * Find the last open program and check which versionType is active.
     *
     * @return Program
     */
    public function findLastProgram(): ?Program
    {
        return $this->getEntityManager()->getRepository(Program::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @param Program $program
     *
     * @return \stdClass
     */
    public function findMinAndMaxYearInProgram(Program $program): \stdClass
    {
        /** @var \Program\Repository\Program $repository */
        $repository = $this->getEntityManager()->getRepository(Program::class);

        $yearSpanResult = $repository->findMinAndMaxYearInProgram($program);
        $yearSpan = new \stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    /**
     * @param Country $country
     *
     * @return Funder[]
     */
    public function findFunderByCountry(Country $country): array
    {
        return $this->getEntityManager()->getRepository(Funder::class)
            ->findBy(['country' => $country], ['position' => 'ASC']);
    }

    /**
     * @param Program $program
     * @param Organisation $organisation
     *
     * @return null|Doa
     */
    public function findProgramDoaByProgramAndOrganisation(Program $program, Organisation $organisation): ?Doa
    {
        return $this->getEntityManager()->getRepository(Doa::class)->findOneBy(
            [
                'program'      => $program,
                'organisation' => $organisation,
            ]
        );
    }
}

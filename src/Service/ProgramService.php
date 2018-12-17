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
use Program\Entity\Call\Call;
use Program\Entity\Doa;
use Program\Entity\Funder;
use Program\Entity\Program;
use Program\ValueObject\ProgramData;
use Project\Entity\Project;

/**
 * Class ProgramService
 *
 * @package Program\Service
 */
class ProgramService extends AbstractService
{
    public function findProgramById(int $id): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->find($id);
    }

    public function findProgramByName(string $name): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->findOneBy(['program' => $name]);
    }

    public function findLastProgram(): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->findOneBy([], ['id' => 'DESC']);
    }

    public function findMinAndMaxYearInProgram(Program $program): \stdClass
    {
        /** @var \Program\Repository\Program $repository */
        $repository = $this->entityManager->getRepository(Program::class);

        $yearSpanResult = $repository->findMinAndMaxYearInProgram($program);
        $yearSpan = new \stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    public function findProgramData(): ProgramData
    {
        $calls = $this->entityManager->getRepository(Call::class)->findAmountOfActiveCalls();
        $years = $this->entityManager->getRepository(Call::class)->findAmountOfYears();
        $organisations = $this->entityManager->getRepository(Organisation::class)->findAmountOfActiveOrganisations();
        $countries = $this->entityManager->getRepository(Country::class)->findAmountOfActiveCountries();
        $projects = $this->entityManager->getRepository(Project::class)->findAmountOfActiveProjects();

        return new ProgramData($calls, $projects, $organisations, $countries, $years);
    }

    public function findFunderByCountry(Country $country): array
    {
        return $this->entityManager->getRepository(Funder::class)
            ->findBy(['country' => $country], ['position' => 'ASC']);
    }

    public function findProgramDoaByProgramAndOrganisation(Program $program, Organisation $organisation): ?Doa
    {
        return $this->entityManager->getRepository(Doa::class)->findOneBy(
            [
                'program'      => $program,
                'organisation' => $organisation,
            ]
        );
    }
}

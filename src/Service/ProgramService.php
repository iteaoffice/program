<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Service;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use General\Entity\Country;
use General\Search\Service\CountrySearchService;
use Organisation\Entity\Organisation;
use Organisation\Search\Service\OrganisationSearchService;
use Program\Entity\Call\Call;
use Program\Entity\Doa;
use Program\Entity\Funder;
use Program\Entity\Program;
use Program\ValueObject\ProgramData;
use Project\Search\Service\ProjectSearchService;
use stdClass;

/**
 * Class ProgramService
 * @package Program\Service
 */
class ProgramService extends AbstractService
{
    private OrganisationSearchService $organisationSearchService;
    private ProjectSearchService $projectSearchService;
    private CountrySearchService $countrySearchService;

    public function __construct(
        EntityManager $entityManager,
        OrganisationSearchService $organisationSearchService,
        ProjectSearchService $projectSearchService,
        CountrySearchService $countrySearchService
    ) {
        parent::__construct($entityManager);

        $this->organisationSearchService = $organisationSearchService;
        $this->projectSearchService      = $projectSearchService;
        $this->countrySearchService      = $countrySearchService;
    }

    public function findProgramById(int $id): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->find($id);
    }

    public function canDeleteProgram(Program $program): bool
    {
        $cannotDeleteProgramReasons = [];

        if (! $program->getCall()->isEmpty()) {
            $cannotDeleteProgramReasons[] = 'This programme has calls';
        }

        if (! $program->getContactDnd()->isEmpty()) {
            $cannotDeleteProgramReasons[] = 'This programme has DND';
        }

        if (! $program->getParentDoa()->isEmpty()) {
            $cannotDeleteProgramReasons[] = 'This programme has Parent DOAs';
        }

        if (! $program->getParentInvoice()->isEmpty()) {
            $cannotDeleteProgramReasons[] = 'This programme has Parent Invoices';
        }

        if (! $program->getParentInvoiceExtra()->isEmpty()) {
            $cannotDeleteProgramReasons[] = 'This programme has Parent Extra Invoices';
        }

        return count($cannotDeleteProgramReasons) === 0;
    }

    public function findProgramByName(string $name): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->findOneBy(['program' => $name]);
    }

    public function findLastProgram(): ?Program
    {
        return $this->entityManager->getRepository(Program::class)->findOneBy([], ['id' => Criteria::DESC]);
    }

    public function findMinAndMaxYearInProgram(Program $program): stdClass
    {
        /** @var \Program\Repository\Program $repository */
        $repository = $this->entityManager->getRepository(Program::class);

        $yearSpanResult    = $repository->findMinAndMaxYearInProgram($program);
        $yearSpan          = new stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    public function findProgramData(): ProgramData
    {
        $calls         = $this->entityManager->getRepository(Call::class)->findAmountOfActiveCalls();
        $years         = $this->entityManager->getRepository(Call::class)->findAmountOfYears();
        $organisations = $this->organisationSearchService->findAmountOfActiveOrganisations();

        $countries = $this->countrySearchService->findAmountOfActiveCountries();
        $projects  = $this->projectSearchService->findAmountOfActiveProjects();

        return new ProgramData($calls, $projects, $organisations, $countries, $years);
    }

    public function findFunderByCountry(Country $country): array
    {
        return $this->entityManager->getRepository(Funder::class)
            ->findBy(['country' => $country], ['position' => Criteria::ASC]);
    }
}

<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Service;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use General\Search\Service\CountrySearchService;
use Organisation\Entity\Organisation;
use Organisation\Search\Service\OrganisationSearchService;
use Program\Entity\Call\Call;
use Program\Entity\Call\Session;
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
    private ContactService $contactService;

    public function __construct(
        EntityManager $entityManager,
        OrganisationSearchService $organisationSearchService,
        ProjectSearchService $projectSearchService,
        CountrySearchService $countrySearchService,
        ContactService $contactService
    ) {
        parent::__construct($entityManager);

        $this->organisationSearchService = $organisationSearchService;
        $this->projectSearchService      = $projectSearchService;
        $this->countrySearchService      = $countrySearchService;
        $this->contactService            = $contactService;
    }

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

    public function findCallSessionsByMeeting(Meeting $meeting): array
    {
        $callSessions = [];
        //Create a local array of available sessions
        if ($meeting->hasIdeaTool()) {
            foreach ($meeting->getIdeaTool() as $tool) {
                foreach ($tool->getSession() as $callSession) {
                    if ($callSession->isOpenForRegistration()) {
                        $callSessions[] = $callSession;
                    }
                }
            }
        }

        return $callSessions;
    }

    public function updateSessionsFromForm(array $selectedSessionIds, Contact $contact): void
    {
        //Handle the form data
        foreach ($selectedSessionIds as $sessionId => $status) {
            /** @var Session $session */
            $session = $this->find(Session::class, (int)$sessionId);

            //User wants to be in
            if ($status === '1' && ! $session->isOverbooked() && ! $this->sessionHasContact($session, $contact)) {
                $participant = new Session\Participant();
                $participant->setContact($contact);
                $participant->setSession($session);

                $this->save($participant);
            }

            //User does not want to be in
            if ($status === '0' && $this->sessionHasContact($session, $contact)) {
                /** @var Session\Participant $participant */
                $participant = $this->entityManager->getRepository(Session\Participant::class)->findOneBy(
                    [
                        'session' => $session,
                        'contact' => $contact,
                    ]
                );
                $this->delete($participant);
            }
        }
    }

    public function sessionHasContact(Session $session, Contact $contact): bool
    {
        $participant = $this->entityManager->getRepository(Session\Participant::class)->findOneBy(
            [
                'session' => $session,
                'contact' => $contact,
            ]
        );

        return null !== $participant;
    }

    public function updateSessionParticipants(Session $session, array $data): void
    {
        $contacts = $data['contacts'] ?? [];

        //Update the contacts
        foreach ($contacts as $contactId) {
            $contact = $this->contactService->findContactById((int)$contactId);

            if (null !== $contact && ! $this->sessionHasContact($session, $contact)) {
                $participant = new Session\Participant();
                $participant->setContact($contact);
                $participant->setSession($session);

                $this->save($participant);
            }
        }

        foreach ($session->getParticipant() as $participant) {
            if (! in_array($participant->getContact()->getId(), $contacts, false)) {
                $this->delete($participant);
            }
        }
    }
}

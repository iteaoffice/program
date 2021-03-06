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

use Admin\Service\AdminService;
use Contact\Entity\Contact;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Laminas\Validator\File\MimeType;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\ValueObject\Calls;
use Program\ValueObject\CallStatus;
use Project\Entity\Version\Type;
use stdClass;

/**
 * Class CallService
 *
 * @package Program\Service
 */
class CallService extends AbstractService
{
    public const PO_CLOSED    = 'PO_CLOSED';
    public const PO_NOT_OPEN  = 'PO_NOT_OPEN';
    public const PO_OPEN      = 'PO_OPEN';
    public const FPP_CLOSED   = 'FPP_CLOSED';
    public const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    public const FPP_OPEN     = 'FPP_OPEN';
    public const UNDEFINED    = 'UNDEFINED';

    private GeneralService $generalService;
    private AdminService $adminService;

    public function __construct(
        EntityManager $entityManager,
        GeneralService $generalService,
        AdminService $adminService
    ) {
        parent::__construct($entityManager);

        $this->generalService = $generalService;
        $this->adminService   = $adminService;
    }

    public function findCallById(int $id): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->find($id);
    }

    public function findCallByName(string $name): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findOneBy(['call' => $name]);
    }

    public function canDeleteCall(Call $call): bool
    {
        $cannotDeleteCallReasons = [];

        if (! $call->getProject()->isEmpty()) {
            $cannotDeleteCallReasons[] = 'This programme call has projects';
        }

        if (! $call->getNda()->isEmpty()) {
            $cannotDeleteCallReasons[] = 'This programme call has NDA';
        }

        if (! $call->getCalendar()->isEmpty()) {
            $cannotDeleteCallReasons[] = 'This programme call has calendar items';
        }

        if ($call->hasIdeaTool()) {
            $cannotDeleteCallReasons[] = 'This programme call has a Project Idea Tool';
        }

        return count($cannotDeleteCallReasons) === 0;
    }

    public function findNotApprovedNda(): ArrayCollection
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return new ArrayCollection($repository->findNotApprovedNda());
    }

    public function findActiveVersionTypeInCall(Call $call): Type
    {
        $today = new DateTime();

        if ($call->getPoOpenDate() < $today && $call->getPoCloseDate() > $today) {
            return $this->entityManager->find(Type::class, Type::TYPE_PO);
        }

        return $this->entityManager->find(Type::class, Type::TYPE_FPP);
    }

    public function findOpenCall(): Calls
    {
        $repository    = $this->entityManager->getRepository(Call::class);
        $openCalls     = $repository->findOpenCalls();
        $upcomingCalls = $repository->findUpcomingCalls();

        return new Calls($openCalls, $upcomingCalls);
    }

    public function findLastActiveCall(): ?Call
    {
        $repository    = $this->entityManager->getRepository(Call::class);
        return $repository->findLastActiveCall();
    }

    public function findNextCall(Call $call): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findNextCall($call);
    }

    public function findPreviousCall(Call $call): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findPreviousCall($call);
    }

    public function hasOpenCallsForNewProject(): bool
    {
        return count($this->findOpenCallsForNewProject()) > 0;
    }

    public function findOpenCallsForNewProject(): array
    {
        $repository = $this->entityManager->getRepository(Call::class);

        return $repository->findOpenCallsForNewProject();
    }

    public function findMinAndMaxYearInCall(Call $call): stdClass
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        $yearSpanResult    = $repository->findMinAndMaxYearInCall($call);
        $yearSpan          = new stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    public function isOpen(Call $call, int $type = null): bool
    {
        //Of no type is given, we assume it is a new project and we derive the
        //type from the call
        if (null === $type) {
            $type = Type::TYPE_FPP;
            if ($call->hasTwoStageProcess()) {
                $type = Type::TYPE_PO;
            }
        }

        switch ($type) {
            case Type::TYPE_PO:
                return $this->getCallStatus($call)->getResult() === self::PO_OPEN;
            case Type::TYPE_FPP:
                return $this->getCallStatus($call)->getResult() === self::FPP_OPEN;
            default:
                return true;
        }
    }

    public function getCallStatus(Call $call): CallStatus
    {
        $type                 = null;
        $today                = new DateTime();
        $dateTime             = new DateTime();
        $notificationDeadline = $dateTime->sub(new DateInterval('P1W'));

        if ($call->getPoOpenDate() > $today) {
            $referenceDate = $call->getPoOpenDate();
            $result        = self::PO_NOT_OPEN;
        } elseif ($call->getPoCloseDate() > $today) {
            $referenceDate = $call->getPoCloseDate();
            $result        = self::PO_OPEN;
        } elseif ($call->getPoCloseDate() > $notificationDeadline && $call->getFppOpenDate() > $today) {
            $referenceDate = $call->getPoCloseDate();
            $result        = self::PO_CLOSED;
        } elseif ($call->getFppOpenDate() > $today) {
            $referenceDate = $call->getFppOpenDate();
            $result        = self::FPP_NOT_OPEN;
        } elseif ($call->getFppCloseDate() > $today) {
            $referenceDate = $call->getFppCloseDate();
            $result        = self::FPP_OPEN;
        } elseif ($call->getFppCloseDate() > $notificationDeadline) {
            $referenceDate = $call->getFppCloseDate();
            $result        = self::FPP_CLOSED;
        } else {
            $referenceDate = null;
            $result        = self::UNDEFINED;
        }

        return new CallStatus($referenceDate, $result);
    }

    public function findFirstAndLastCall(): stdClass
    {
        $firstCall           = $this->entityManager->getRepository(Call::class)->findOneBy([], ['id' => 'ASC']);
        $lastCall            = $this->entityManager->getRepository(Call::class)->findOneBy([], ['id' => 'DESC']);
        $callSpan            = new stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall  = $lastCall;

        return $callSpan;
    }

    public function findProjectAndPartners(Call $call): array
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        return $repository->findProjectAndPartners($call);
    }

    public function findNdaByCallAndContact(Call $call, Contact $contact): ?Nda
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return $repository->findNdaByCallAndContact($call, $contact);
    }

    public function findNdaByContact(Contact $contact): ?Nda
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return $repository->findNdaByContact($contact);
    }

    public function isNdaValid(Nda $nda): bool
    {
        if (null === $nda->getDateSigned()) {
            return false;
        }

        $today       = new DateTime();
        $twoYearsAgo = $today->sub(new DateInterval('P2Y'));

        return $nda->getDateSigned() > $twoYearsAgo;
    }

    public function uploadNda(array $file, Contact $contact, Call $call = null): Nda
    {
        $nda = new Nda();
        $nda->setContact($contact);
        if (null !== $call) {
            $nda->setCall([$call]);
        }

        if ($file['error'] === 0) {
            $ndaObject = new NdaObject();
            $ndaObject->setObject(file_get_contents($file['tmp_name']));

            $nda->setSize($file['size']);

            $fileTypeValidator = new MimeType();
            $fileTypeValidator->isValid($file);
            $nda->setContentType($this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type));

            $ndaObject->setNda($nda);
            $this->save($ndaObject);
        } else {
            $this->save($nda);
        }

        return $nda;
    }

    public function submitNda(Contact $contact, Call $call = null): Nda
    {
        $nda = new Nda();
        $nda->setContact($contact);
        $nda->setApprover($contact);
        $nda->setDateSigned(new DateTime());
        $nda->setDateApproved(new DateTime());
        if (null !== $call) {
            $nda->setCall([$call]);
        }

        $this->save($nda);

        $this->adminService->flushPermitsByContact($contact);

        return $nda;
    }
}

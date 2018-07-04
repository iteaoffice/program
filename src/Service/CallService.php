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

use Admin\Service\AdminService;
use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use General\Entity\Country;
use General\Service\GeneralService;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Entity\Program;
use Project\Entity\Version\Type;
use Zend\Stdlib\ArrayObject;
use Zend\Validator\File\MimeType;

/**
 * Class CallService
 *
 * @package Program\Service
 */
class CallService extends AbstractService
{
    public const PO_CLOSED = 'PO_CLOSED';
    public const PO_NOT_OPEN = 'PO_NOT_OPEN';
    public const PO_OPEN = 'PO_OPEN';
    public const FPP_CLOSED = 'FPP_CLOSED';
    public const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    public const FPP_OPEN = 'FPP_OPEN';
    public const UNDEFINED = 'UNDEFINED';

    /**
     * @var GeneralService
     */
    private $generalService;
    /**
     * @var AdminService
     */
    private $adminService;

    public function __construct(
        EntityManager $entityManager,
        GeneralService $generalService,
        AdminService $adminService
    ) {
        parent::__construct($entityManager);

        $this->generalService = $generalService;
        $this->adminService = $adminService;
    }

    public function findCallById(int $id): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->find($id);
    }

    public function findCallByName(string $name): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findOneBy(['call' => $name]);
    }

    public function findNotApprovedNda(): ArrayCollection
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return new ArrayCollection($repository->findNotApprovedNda());
    }

    public function findNextCall(Call $call): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findNextCall($call);
    }

    public function findPreviousCall(Call $call): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findPreviousCall($call);
    }

    public function findOpenCall($type): ?Call
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        return $repository->findOpenCall($type);
    }

    public function findMinAndMaxYearInCall(Call $call): \stdClass
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        $yearSpanResult = $repository->findMinAndMaxYearInCall($call);
        $yearSpan = new \stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return \stdClass
     */
    public function findLastCallAndActiveVersionType(): \stdClass
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        $result = $repository->findLastCallAndActiveVersionType();
        $lastCallAndActiveVersionType = new \stdClass();
        $lastCallAndActiveVersionType->call = $result['call'];
        $lastCallAndActiveVersionType->versionType = $this->entityManager->find(Type::class, $result['versionType']);

        return $lastCallAndActiveVersionType;
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return Call
     */
    public function findLastCall(): ?Call
    {
        return $this->entityManager->getRepository(Call::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return Call|null
     */
    public function findLastActiveCall(): ?Call
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        $notEmptyCalls = $repository->findActiveCalls();

        if (\count($notEmptyCalls) > 0) {
            return end($notEmptyCalls);
        }

        return null;
    }

    public function isOpen(Call $call, Type $type): bool
    {
        switch ($type->getId()) {
            case Type::TYPE_PO:
                return $this->getCallStatus($call)->result === self::PO_OPEN;
            case Type::TYPE_FPP:
                return $this->getCallStatus($call)->result === self::FPP_OPEN;
            default:
                return true;
        }
    }

    /**
     * Return the current status of the given all with given the current date
     * Return a status and the relevant date.
     *
     * @param Call         $call
     *
     * @return ArrayObject
     *
     * @property \DateTime $result
     * @method \DateTime $referenceDate
     */
    public function getCallStatus(Call $call): ArrayObject
    {
        $callStatus = new ArrayObject();
        /*
         * Go over the dates and find the most suited date.
         */
        $type = null;
        $today = new \DateTime();
        $dateTime = new \DateTime();
        $notificationDeadline = $dateTime->sub(new \DateInterval("P1W"));

        if ($call->getPoOpenDate() > $today) {
            $referenceDate = $call->getPoOpenDate();
            $result = self::PO_NOT_OPEN;
            $type = Type::TYPE_PO;
        } elseif ($call->getPoCloseDate() > $today) {
            $referenceDate = $call->getPoCloseDate();
            $result = self::PO_OPEN;
            $type = Type::TYPE_PO;
        } elseif ($call->getPoCloseDate() > $notificationDeadline
            && $call->getFppOpenDate() > $today
        ) {
            $referenceDate = $call->getPoCloseDate();
            $result = self::PO_CLOSED;
            $type = Type::TYPE_PO;
        } elseif ($call->getFppOpenDate() > $today) {
            $referenceDate = $call->getFppOpenDate();
            $result = self::FPP_NOT_OPEN;
            $type = Type::TYPE_FPP;
        } elseif ($call->getFppCloseDate() > $today) {
            $referenceDate = $call->getFppCloseDate();
            $result = self::FPP_OPEN;
            $type = Type::TYPE_FPP;
        } elseif ($call->getFppCloseDate() > $notificationDeadline) {
            $referenceDate = $call->getFppCloseDate();
            $result = self::FPP_CLOSED;
            $type = Type::TYPE_FPP;
        } else {
            $referenceDate = null;
            $result = self::UNDEFINED;
            $type = Type::TYPE_CR;
        }

        $callStatus->result = $result;
        $callStatus->type = $this->entityManager->find(Type::class, $type);
        $callStatus->referenceDate = $referenceDate;

        return $callStatus;
    }

    public function requireDoaPerProject(Call $call): bool
    {
        return $call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROJECT;
    }

    public function requireDoaPerProgram(Call $call): bool
    {
        return $call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROGRAM;
    }

    public function requireLoi(Call $call): bool
    {
        return $call->getLoiRequirement() === Call::LOI_REQUIRED;
    }

    public function findFirstAndLastCall(): \stdClass
    {
        $firstCall = $this->entityManager->getRepository(Call::class)->findOneBy([], ['id' => 'ASC']);
        $lastCall = $this->entityManager->getRepository(Call::class)->findOneBy([], ['id' => 'DESC']);
        $callSpan = new \stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall = $lastCall;

        return $callSpan;
    }

    /**
     * @param Program|null $program
     *
     * @return Call[]
     */
    public function findNonEmptyAndActiveCalls(Program $program = null): array
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        return $repository->findNonEmptyAndActiveCalls($program);
    }

    /**
     * @param Call $call
     *
     * @return array
     */
    public function findProjectAndPartners(Call $call): array
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->entityManager->getRepository(Call::class);

        return $repository->findProjectAndPartners($call);
    }

    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return null|Nda
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact): ?Nda
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return $repository->findNdaByCallAndContact($call, $contact);
    }

    /**
     * @param Contact $contact
     *
     * @return null|Nda
     */
    public function findNdaByContact(Contact $contact): ?Nda
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->entityManager->getRepository(Nda::class);

        return $repository->findNdaByContact($contact);
    }

    /**
     * @param Nda $nda
     *
     * @return bool
     */
    public function isNdaValid(Nda $nda): bool
    {
        if (null === $nda->getDateSigned()) {
            return false;
        }

        $today = new \DateTime();
        $twoYearsAgo = $today->sub(new \DateInterval('P2Y'));

        return $nda->getDateSigned() > $twoYearsAgo;
    }

    /**
     * @param Call $call
     *
     * @return Country[]
     */
    public function findCountryByCall(Call $call): array
    {
        return $this->generalService->findCountryByCall($call);
    }

    /**
     * Upload a NDA to the system and store it for the user.
     *
     * @param array   $file
     * @param Contact $contact
     * @param Call    $call
     *
     * @return Nda
     */
    public function uploadNda(array $file, Contact $contact, Call $call = null): Nda
    {
        $ndaObject = new NdaObject();
        $ndaObject->setObject(file_get_contents($file['tmp_name']));
        $nda = new Nda();
        $nda->setContact($contact);
        if (null !== $call) {
            $nda->setCall([$call]);
        }
        $nda->setSize($file['size']);

        $fileTypeValidator = new MimeType();
        $fileTypeValidator->isValid($file);
        $nda->setContentType($this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type));

        $ndaObject->setNda($nda);
        $this->save($ndaObject);

        return $ndaObject->getNda();
    }

    /**
     * @param Contact   $contact
     * @param Call|null $call
     *
     * @return Nda
     */
    public function submitNda(Contact $contact, Call $call = null): Nda
    {
        $nda = new Nda();
        $nda->setContact($contact);
        $nda->setApprover($contact);
        $nda->setDateSigned(new \DateTime());
        $nda->setDateApproved(new \DateTime());
        if (null !== $call) {
            $nda->setCall([$call]);
        }

        $this->save($nda);

        $this->adminService->flushPermitsByContact($contact);

        return $nda;
    }
}

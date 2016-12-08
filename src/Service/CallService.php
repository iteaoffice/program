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

use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use General\Entity\ContentType;
use General\Entity\Country;
use Program\Entity\Call\Call;
use Program\Entity\EntityAbstract;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Entity\Program;
use Project\Entity\Version\Type;
use Zend\Stdlib\ArrayObject;

/**
 * Class CallService
 *
 * @package Program\Service
 */
class CallService extends ServiceAbstract
{
    const PO_CLOSED = 'PO_CLOSED';
    const PO_NOT_OPEN = 'PO_NOT_OPEN';
    const PO_GRACE = 'PO_GRACE';
    const PO_OPEN = 'PO_OPEN';
    const FPP_CLOSED = 'FPP_CLOSED';
    const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    const FPP_OPEN = 'FPP_OPEN';
    const FPP_GRACE = 'FPP_GRACE';
    const UNDEFINED = 'UNDEFINED';

    /**
     * @param $id
     *
     * @return null|Call
     */
    public function findCallById($id)
    {
        return $this->getEntityManager()->getRepository(Call::class)->find($id);
    }

    /**
     * @param string $name
     *
     * @return null|Call
     */
    public function findCallByName($name)
    {
        return $this->getEntityManager()->getRepository(Call::class)->findOneBy(['call' => $name]);
    }

    /**
     * Get a list of not approved lois.
     *
     * @return Nda[]|ArrayCollection
     */
    public function findNotApprovedNda()
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->getEntityManager()->getRepository(Nda::class);

        return new ArrayCollection($repository->findNotApprovedNda());
    }

    /**
     * Find the open call based on the request type.
     *
     * @param int $type ;
     *
     * @return Call|null
     */
    public function findOpenCall($type)
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        return $repository->findOpenCall($type);
    }

    /**
     * @param Call $call
     *
     * @return \stdClass
     */
    public function findMinAndMaxYearInCall(Call $call)
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        $yearSpanResult    = $repository->findMinAndMaxYearInCall($call);
        $yearSpan          = new \stdClass();
        $yearSpan->minYear = (int)$yearSpanResult['minYear'];
        $yearSpan->maxYear = (int)$yearSpanResult['maxYear'];

        return $yearSpan;
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return \stdClass
     */
    public function findLastCallAndActiveVersionType()
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        $result                                    = $repository->findLastCallAndActiveVersionType();
        $lastCallAndActiveVersionType              = new \stdClass();
        $lastCallAndActiveVersionType->call        = $result['call'];
        $lastCallAndActiveVersionType->versionType = $this->getVersionService()
            ->findEntityById(Type::class, $result['versionType']);

        return $lastCallAndActiveVersionType;
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return Call
     */
    public function findLastCall()
    {
        return $this->getEntityManager()->getRepository(Call::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return Call|null
     */
    public function findLastActiveCall()
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        $notEmptyCalls = $repository->findNonEmptyCalls();

        if (count($notEmptyCalls) > 0) {
            return end($notEmptyCalls);
        }

        return null;
    }


    /**
     * Return true when the call is open specified for the given type.
     *
     * @param Call $call
     * @param Type $type
     *
     * @return bool
     */
    public function isOpen(Call $call, Type $type)
    {
        switch ($type->getId()) {
            case Type::TYPE_PO:
                return in_array($this->getCallStatus($call)->result, [self::PO_GRACE, self::PO_OPEN]);
            case Type::TYPE_FPP:
                return in_array($this->getCallStatus($call)->result, [self::FPP_OPEN, self::FPP_GRACE]);
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
    public function getCallStatus(Call $call)
    {
        $callStatus = new ArrayObject();
        /*
         * Go over the dates and find the most suited date.
         */
        $type                 = null;
        $today                = new \DateTime();
        $dateTime             = new \DateTime();
        $notificationDeadline = $dateTime->sub(new \DateInterval("P1W"));

        if ($call->getPoOpenDate() > $today) {
            $referenceDate = $call->getPoOpenDate();
            $result        = self::PO_NOT_OPEN;
            $type          = Type::TYPE_PO;
        } elseif ($call->getPoCloseDate() > $today) {
            $referenceDate = $call->getPoCloseDate();
            $result        = self::PO_OPEN;
            $type          = Type::TYPE_PO;
        } elseif ($call->getPoGraceDate() > $today) {
            $referenceDate = $call->getPoCloseDate();
            $result        = self::PO_GRACE;
            $type          = Type::TYPE_PO;
        } elseif ($call->getPoCloseDate() > $notificationDeadline
            && $call->getFppOpenDate() > $today
        ) {
            $referenceDate = $call->getPoCloseDate();
            $result        = self::PO_CLOSED;
            $type          = Type::TYPE_PO;
        } elseif ($call->getFppOpenDate() > $today) {
            $referenceDate = $call->getFppOpenDate();
            $result        = self::FPP_NOT_OPEN;
            $type          = Type::TYPE_FPP;
        } elseif ($call->getFppCloseDate() > $today) {
            $referenceDate = $call->getFppCloseDate();
            $result        = self::FPP_OPEN;
            $type          = Type::TYPE_FPP;
        } elseif ($call->getPoGraceDate() > $today) {
            $referenceDate = $call->getFppCloseDate();
            $result        = self::FPP_GRACE;
            $type          = Type::TYPE_FPP;
        } elseif ($call->getFppCloseDate() > $notificationDeadline) {
            $referenceDate = $call->getFppCloseDate();
            $result        = self::FPP_CLOSED;
            $type          = Type::TYPE_FPP;
        } else {
            $referenceDate = null;
            $result        = self::UNDEFINED;
            $type          = Type::TYPE_CR;
        }

        $callStatus->result        = $result;
        $callStatus->type          = $this->getVersionService()->findEntityById(Type::class, $type);
        $callStatus->referenceDate = $referenceDate;

        return $callStatus;
    }

    /**
     * Return true when the call is in grace mode.
     *
     * @param Call $call
     *
     * @return bool
     */
    public function isGrace(Call $call)
    {
        return in_array($this->getCallStatus($call)->result, [self::PO_GRACE, self::FPP_GRACE]);
    }

    /**
     * Returns true when a DOA per partner is required.
     *
     * @param Call $call
     *
     * @return bool
     */
    public function requireDoaPerProject(Call $call)
    {
        return $call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROJECT;
    }

    /**
     * @param Call $call
     *
     * @return bool
     */
    public function requireDoaPerProgram(Call $call)
    {
        return $call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROGRAM;
    }

    /**
     * Return an object with the first and last call in the database.
     *
     * @return \stdClass
     */
    public function findFirstAndLastCall()
    {
        $firstCall           = $this->getEntityManager()->getRepository(Call::class)->findOneBy([], ['id' => 'ASC']);
        $lastCall            = $this->getEntityManager()->getRepository(Call::class)->findOneBy([], ['id' => 'DESC']);
        $callSpan            = new \stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall  = $lastCall;

        return $callSpan;
    }

    /**
     * @param Program|null $program
     *
     * @return mixed
     */
    public function findNonEmptyCalls(Program $program = null)
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        return $repository->findNonEmptyCalls($program);
    }

    /**
     * @param Call $call
     *
     * @return mixed
     */
    public function findProjectAndPartners(Call $call)
    {
        /** @var \Program\Repository\Call\Call $repository */
        $repository = $this->getEntityManager()->getRepository(Call::class);

        return $repository->findProjectAndPartners($call);
    }

    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return null|\Program\Entity\Nda
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact)
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->getEntityManager()->getRepository(Nda::class);

        return $repository->findNdaByCallAndContact($call, $contact);
    }

    /**
     * @param Contact $contact
     *
     * @return null|\Program\Entity\Nda
     */
    public function findNdaByContact(Contact $contact)
    {
        /** @var \Program\Repository\Nda $repository */
        $repository = $this->getEntityManager()->getRepository(Nda::class);

        return $repository->findNdaByContact($contact);
    }

    /**
     * @param Nda $nda
     *
     * @return bool
     */
    public function isNdaValid(Nda $nda)
    {
        if (is_null($nda->getDateSigned())) {
            return false;
        }

        $today       = new \DateTime();
        $twoYearsAgo = $today->sub(new \DateInterval('P2Y'));

        return $nda->getDateSigned() > $twoYearsAgo;
    }

    /**
     * @param Call $call
     *
     * @return Country[]
     */
    public function findCountryByCall(Call $call)
    {
        return $this->getGeneralService()->findCountryByCall($call);
    }

    /**
     * @param string $entity
     * @param        $docRef
     *
     * @throws \InvalidArgumentException
     *
     * @return EntityAbstract
     */
    public function findEntityByDocRef($entity, $docRef)
    {
        return $this->getEntityManager()->getRepository($entity)->findOneBy(['docRef' => $docRef]);
    }

    /**
     * @param Call $call
     *
     * @return mixed
     */
    public function findProjectByCall(Call $call, $which)
    {
        return $this->getProjectService()->findProjectsByCall($call, $which);
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
    public function uploadNda(array $file, Contact $contact, Call $call = null)
    {
        $ndaObject = new NdaObject();
        $ndaObject->setObject(file_get_contents($file['tmp_name']));
        $nda = new Nda();
        $nda->setContact($contact);
        if ( ! is_null($call)) {
            $nda->setCall([$call]);
        }
        $nda->setSize($file['size']);
        $contentType = $this->getGeneralService()->findContentTypeByContentTypeName($file['type']);
        if (is_null($contentType)) {
            $contentType = $this->getGeneralService()->findEntityById(ContentType::class, 0);
        }
        $nda->setContentType($contentType);
        $ndaObject->setNda($nda);
        $this->newEntity($ndaObject);

        return $ndaObject->getNda();
    }
}

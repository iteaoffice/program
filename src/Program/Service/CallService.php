<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Program\Service;

use Affiliation\Service\AffiliationService;
use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Program\Entity\Call\Call;
use Program\Entity\EntityAbstract;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Entity\Program;
use Project\Entity\Version\Type;
use Project\Service\ProjectService;
use Zend\Stdlib\ArrayObject;

/**
 * CallService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
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
     * @var Call
     */
    protected $call;
    /**
     * @var ArrayObject
     */
    protected $callStatus = null;

    /**
     * @param $id
     *
     * @return $this
     */
    public function setCallId($id)
    {
        $this->setCall($this->findEntityById('Call\Call', $id));

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->call) || is_null($this->call->getId());
    }

    /**
     * Get a list of not approved lois.
     *
     * @return Nda[]|ArrayCollection
     */
    public function findNotApprovedNda()
    {
        return new ArrayCollection($this->getEntityManager()->getRepository($this->getFullEntityName('nda'))->findNotApprovedNda());
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
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))->findOpenCall($type);
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get(ProjectService::class);
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService()
    {
        return $this->getServiceLocator()->get(AffiliationService::class);
    }

    /**
     * Find the last open call and check which versionType is active.
     *
     * @return \stdClass
     * @property Call call
     * @property VersionType versionType
     */
    public function findLastCallAndActiveVersionType()
    {
        $result = $this->getEntityManager()->getRepository(
            $this->getFullEntityName('Call\Call')
        )->findLastCallAndActiveVersionType();
        $lastCallAndActiveVersionType = new \stdClass();
        $lastCallAndActiveVersionType->call = $result['call'];
        $lastCallAndActiveVersionType->versionType = $this->getVersionService()->findEntityById(
            'Version\Type',
            $result['versionType']
        );

        return $lastCallAndActiveVersionType;
    }

    /**
     * Return true when the call is open specified for the given type.
     *
     * @param $type ;
     *
     * @return bool
     */
    public function isOpen(Type $type)
    {
        switch ($type->getId()) {
            case Type::TYPE_PO:
                return in_array(
                    $this->getCallStatus()->result,
                    [self::PO_GRACE, self::PO_OPEN]
                );
            case Type::TYPE_FPP:
                return in_array(
                    $this->getCallStatus()->result,
                    [self::FPP_OPEN, self::FPP_GRACE]
                );
            default:
                return true;
        }
    }

    /**
     * Return true when the call is in grace mode.
     *
     * @return bool
     */
    public function isGrace()
    {
        return in_array($this->getCallStatus()->result, [self::PO_GRACE, self::FPP_GRACE]);
    }

    /**
     * Returns true when a DOA per partner is required.
     *
     * @return bool
     */
    public function requireDoaPerProject()
    {
        return $this->call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROJECT;
    }

    public function requireDoaPerProgram()
    {
        return $this->call->getDoaRequirement() === Call::DOA_REQUIREMENT_PER_PROGRAM;
    }

    /**
     * Return an object with the first and last call in the database.
     *
     * @return \stdClass
     */
    public function findFirstAndLastCall()
    {
        $firstCall = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))
            ->findOneBy(
                [],
                ['id' => 'ASC']
            );
        $lastCall = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))
            ->findOneBy(
                [],
                ['id' => 'DESC']
            );
        $callSpan = new \stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall = $lastCall;

        return $callSpan;
    }

    /**
     * Return all calls which have at least one project.
     *
     * @return Call[]
     */
    public function findNonEmptyCalls(Program $program = null)
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('Call\Call')
        )->findNonEmptyCalls($program);
    }

    /**
     * @return mixed
     */
    public function findProjectAndPartners()
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('Call\Call')
        )->findProjectAndPartners($this->getCall());
    }

    /**
     * Return the current status of the given all with given the current date
     * Return a status and the relevant date.
     *
     * @return ArrayObject
     *
     * @method \DateTime $result
     * @method \DateTime $referenceDate
     */
    public function getCallStatus()
    {
        if (is_null($this->callStatus)) {
            if ($this->isEmpty()) {
                throw new \InvalidArgumentException("The call cannot be empty to determine the status");
            }

            $this->callStatus = new ArrayObject();
            /*
             * Go over the dates and find the most suited date.
             */
            $type = null;
            $today = new \DateTime();
            $dateTime = new \DateTime();
            $notificationDeadline = $dateTime->sub(new \DateInterval("P1W"));

            if ($this->getCall()->getPoOpenDate() > $today) {
                $referenceDate = $this->getCall()->getPoOpenDate();
                $result = self::PO_NOT_OPEN;
                $type = Type::TYPE_PO;
            } elseif ($this->getCall()->getPoCloseDate() > $today) {
                $referenceDate = $this->getCall()->getPoCloseDate();
                $result = self::PO_OPEN;
                $type = Type::TYPE_PO;
            } elseif ($this->getCall()->getPoGraceDate() > $today) {
                $referenceDate = $this->getCall()->getPoCloseDate();
                $result = self::PO_GRACE;
                $type = Type::TYPE_PO;
            } elseif ($this->getCall()->getPoCloseDate() > $notificationDeadline and
                $this->getCall()->getFppOpenDate() > $today
            ) {
                $referenceDate = $this->getCall()->getPoCloseDate();
                $result = self::PO_CLOSED;
                $type = Type::TYPE_PO;
            } elseif ($this->getCall()->getFppOpenDate() > $today) {
                $referenceDate = $this->getCall()->getFppOpenDate();
                $result = self::FPP_NOT_OPEN;
                $type = Type::TYPE_FPP;
            } elseif ($this->getCall()->getFppCloseDate() > $today) {
                $referenceDate = $this->getCall()->getFppCloseDate();
                $result = self::FPP_OPEN;
                $type = Type::TYPE_FPP;
            } elseif ($this->getCall()->getPoGraceDate() > $today) {
                $referenceDate = $this->getCall()->getFppCloseDate();
                $result = self::FPP_GRACE;
                $type = Type::TYPE_FPP;
            } elseif ($this->getCall()->getFppCloseDate() > $notificationDeadline) {
                $referenceDate = $this->getCall()->getFppCloseDate();
                $result = self::FPP_CLOSED;
                $type = Type::TYPE_FPP;
            } else {
                $referenceDate = null;
                $result = self::UNDEFINED;
                $type = Type::TYPE_CR;
            }

            $this->callStatus->result = $result;
            $this->callStatus->type = $this->getVersionService()->findEntityById('Version\Type', $type);
            $this->callStatus->referenceDate = $referenceDate;
        }

        return $this->callStatus;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param $call
     *
     * @return $this
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return null|\Program\Entity\Nda
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('nda'))->findNdaByCallAndContact(
            $call,
            $contact
        );
    }

    /**
     * @param Contact $contact
     *
     * @return null|\Program\Entity\Nda
     */
    public function findNdaByContact(Contact $contact)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('nda'))->findNdaByContact(
            $contact
        );
    }

    /**
     * Returns the Call.
     *
     * @return string
     */
    public function parseCall()
    {
        return $this->getCall()->getCall();
    }

    /**
     * @param Call $call
     *
     * @return mixed
     */
    public function findCountryByCall(Call $call)
    {
        return $this->getGeneralService()->findCountryByCall(
            $call
        );
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
        if (is_null($entity)) {
            throw new \InvalidArgumentException("An entity is required to find an entity");
        }
        if (is_null($docRef)) {
            throw new \InvalidArgumentException("A docRef is required to find an entity");
        }
        $entity = $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findOneBy(
            ['docRef' => $docRef]
        );

        return $entity;
    }

    /**
     * @param Call $call
     *
     * @return mixed
     */
    public function findProjectByCall(Call $call, $which)
    {
        return $this->getProjectService()->findProjectsByCall(
            $call,
            $which
        );
    }

    /**
     * Upload a NDA to the system and store it for the user.
     *
     * @param array   $file
     * @param Contact $contact
     * @param Call    $call
     *
     * @return NdaObject
     */
    public function uploadNda(array $file, Contact $contact, Call $call = null)
    {
        $ndaObject = new NdaObject();
        $ndaObject->setObject(file_get_contents($file['tmp_name']));
        $nda = new Nda();
        $nda->setContact($contact);
        if (!is_null($call)) {
            $nda->setCall([$call]);
        }
        $nda->setSize($file['size']);
        $contentType = $this->getGeneralService()->findContentTypeByContentTypeName($file['type']);
        if (is_null($contentType)) {
            $contentType = $this->getGeneralService()->findEntityById('ContentType', 0);
        }
        $nda->setContentType($contentType);
        $ndaObject->setNda($nda);
        $this->newEntity($ndaObject);

        return $ndaObject->getNda();
    }
}

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

use Contact\Entity\Contact;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;

/**
 * CallService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class CallService extends ServiceAbstract
{
    const FPP_CLOSED   = 'FPP_CLOSED';
    const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    const FPP_OPEN     = 'FPP_OPEN';
    const PO_CLOSED    = 'PO_CLOSED';
    const PO_NOT_OPEN  = 'PO_NOT_OPEN';
    const PO_OPEN      = 'PO_OPEN';
    const UNDEFINED    = 'UNDEFINED';
    /**
     * @var Call
     */
    protected $call;

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
     * Find the open call based on the request type
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
     * Find the last open call and check which versionType is active
     *
     * @return \stdClass
     */
    public function findLastCallAndActiveVersionType()
    {
        $result                                    = $this->getEntityManager()->getRepository(
            $this->getFullEntityName('Call\Call')
        )->findLastCallAndActiveVersionType();
        $lastCallAndActiveVersionType              = new \stdClass();
        $lastCallAndActiveVersionType->call        = $result['call'];
        $lastCallAndActiveVersionType->versionType = $this->getVersionService()->findEntityById(
            'Version\Type',
            $result['versionType']
        );

        return $lastCallAndActiveVersionType;
    }

    /**
     * Return an object with the first and last call in the database
     *
     * @return \stdClass
     */
    public function findFirstAndLastCall()
    {
        $firstCall           = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))
                                    ->findOneBy(
                                        [],
                                        array('id' => 'ASC')
                                    );
        $lastCall            = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))
                                    ->findOneBy(
                                        [],
                                        array('id' => 'DESC')
                                    );
        $callSpan            = new \stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall  = $lastCall;

        return $callSpan;
    }

    /**
     * Return all calls which have at least one project
     *
     * @return Call[]
     */
    public function findNonEmptyCalls()
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('Call\Call')
        )->findNonEmptyCalls();
    }

    /**
     * Return the current status of the given all with given the current date
     * Return a status and the relevant date
     *
     * @return \stdClass
     * @method \DateTime $result
     * @method \DateTime $referenceDate
     *
     */
    public function getCallStatus()
    {
        /**
         * Go over the dates and find the most suited date.
         */
        $today                = $dateTime = new \DateTime();
        $notificationDeadline = $dateTime->sub(new \DateInterval("P1W"));
        if ($this->getCall()->getPoOpenDate() > $today) {
            $referenceDate = $this->getCall()->getPoOpenDate();
            $result        = self::PO_NOT_OPEN;
        } elseif ($this->getCall()->getPoCloseDate() > $today) {
            $referenceDate = $this->getCall()->getPoCloseDate();
            $result        = self::PO_OPEN;
        } elseif ($this->getCall()->getPoCloseDate() > $notificationDeadline) {
            $referenceDate = $this->getCall()->getPoCloseDate();
            $result        = self::PO_CLOSED;
        } elseif ($this->getCall()->getFppOpenDate() > $today) {
            $referenceDate = $this->getCall()->getFppOpenDate();
            $result        = self::FPP_NOT_OPEN;
        } elseif ($this->getCall()->getFppCloseDate() > $today) {
            $referenceDate = $this->getCall()->getFppCloseDate();
            $result        = self::FPP_OPEN;
        } elseif ($this->getCall()->getFppCloseDate() > $notificationDeadline) {
            $referenceDate = $this->getCall()->getFppCloseDate();
            $result        = self::FPP_CLOSED;
        } else {
            $referenceDate = null;
            $result        = self::UNDEFINED;
        }
        $callStatus                = new \stdClass();
        $callStatus->result        = $result;
        $callStatus->referenceDate = $referenceDate;

        return $callStatus;
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
     * Upload a NDA to the system and store it for the user
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
            $nda->setCall(array($call));
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

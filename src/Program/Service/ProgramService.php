<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Service;

use General\Entity\Country;
use General\Service\GeneralService;

use Contact\Entity\Contact;
use Program\Entity\Funder;
use Program\Entity\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;

/**
 * ProgramService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class ProgramService extends ServiceAbstract
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var GeneralService
     */
    protected $generalService;

    /**
     * @param Country $country
     *
     * @return Funder[]
     */
    public function findFunderByCountry(Country $country)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('funder'))->findBy(
            array('country' => $country)
        );
    }

    /**
     * Return an object with the first and last call in the database
     *
     * @return \stdClass
     */
    public function findFirstAndLastCall()
    {
        $firstCall = $this->getEntityManager()->getRepository($this->getFullEntityName('call'))->findOneBy(
            array(),
            array('call' => 'ASC')
        );
        $lastCall  = $this->getEntityManager()->getRepository($this->getFullEntityName('call'))->findOneBy(
            array(),
            array('call' => 'DESC')
        );

        $callSpan            = new \stdClass();
        $callSpan->firstCall = $firstCall;
        $callSpan->lastCall  = $lastCall;

        return $callSpan;
    }

    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return \Program\Entity\Nda
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('nda'))->findOneBy(
            array(
                'call'    => $call,
                'contact' => $contact
            )
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
        $nda->setCall($call);
        $nda->setSize($file['size']);
        $nda->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($file['type']));

        $ndaObject->setNda($nda);

        return $this->newEntity($ndaObject);
    }

    /**
     * @param \General\Service\GeneralService $generalService
     */
    public function setGeneralService($generalService)
    {
        $this->generalService = $generalService;
    }

    /**
     * @return \General\Service\GeneralService
     */
    public function getGeneralService()
    {
        if (is_null($this->generalService)) {
            $this->setGeneralService($this->getServiceLocator()->get('general_general_service'));
        }

        return $this->generalService;
    }
}

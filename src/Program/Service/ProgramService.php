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

use Contact\Entity\Contact;
use Program\Entity\Funder;
use Program\Entity\Call;

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
     * Find 1 entity based on the name
     *
     * @param   $entity
     * @param   $name
     *
     * @return object
     */
    public function findEntityByName($entity, $name)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findOneBy(
            array('name' => $name)
        );
    }

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
}

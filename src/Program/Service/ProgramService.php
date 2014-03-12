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

use General\Entity\Country;
use General\Service\GeneralService;
use Organisation\Entity\Organisation;
use Program\Entity\Program;
use Project\Service\VersionService;

use Contact\Entity\Contact;
use Program\Entity\Funder;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Project\Entity\Version\Type;

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
     * @param Program      $program
     * @param Organisation $organisation
     *
     * @return null|\Program\Entity\ProgramDoa
     */
    public function findProgramDoaByProgramAndOrganisation(Program $program, Organisation $organisation)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('ProgramDoa'))->findOneBy(
            array(
                'program'      => $program,
                'organisation' => $organisation
            )
        );
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
        $result = $this->getEntityManager()->getRepository(
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
        $firstCall = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))->findOneBy(
            array(),
            array('call' => 'ASC')
        );
        $lastCall  = $this->getEntityManager()->getRepository($this->getFullEntityName('Call\Call'))->findOneBy(
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
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get('general_general_service');
    }

    /**
     * get the version service
     *
     * @return VersionService
     */
    public function getVersionService()
    {
        return $this->getServiceLocator()->get('project_version_service');
    }
}

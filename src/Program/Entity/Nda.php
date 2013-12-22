<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation AS Gedmo;

/**
 * Entity for a nda
 *
 * @ORM\Table(name="nda")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("nda")
 *
 * @category    Contact
 * @package     Entity
 */
class Nda extends EntityAbstract
{
    /**
     * @ORM\Column(name="nda_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateApproved;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $dateSigned;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programNna")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-nda-file"})
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="size", type="integer", nullable=false)
     * @var integer
     */
    private $size;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade={"persist"}, inversedBy="nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")
     * })
     * @var \Program\Entity\Call\Call
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\NdaObject", cascade={"persist"}, mappedBy="nda")
     * @Annotation\Exclude()
     * @var \Program\Entity\NdaObject
     */
    private $object;

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * ToString
     * @return string
     */
    public function __toString()
    {
        return $this->domain;
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'id'           => $this->id,
            'dateApproved' => $this->dateApproved,
            'dateSigned'   => $this->dateSigned,
            'contentType'  => $this->contentType,
            'size'         => $this->size,
            'dateCreated'  => $this->dateCreated,
            'dateUpdated'  => $this->dateUpdated,
            'contact'      => $this->contact,
            'call'         => $this->call,
            'object'       => $this->object,
        );
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception(sprintf("This class %s is unused", __CLASS__));
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'dateApproved',
                        'required' => false,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'contact',
                        'required' => true,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'call',
                        'required' => true,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'program',
                        'required' => true,
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param \Program\Entity\Call\Call $call
     */
    public function setCall($call)
    {
        $this->call = $call;
    }

    /**
     * @return \Program\Entity\Call\Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \General\Entity\ContentType $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \General\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param \DateTime $dateApproved
     */
    public function setDateApproved($dateApproved)
    {
        $this->dateApproved = $dateApproved;
    }

    /**
     * @return \DateTime
     */
    public function getDateApproved()
    {
        return $this->dateApproved;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateSigned
     */
    public function setDateSigned($dateSigned)
    {
        $this->dateSigned = $dateSigned;
    }

    /**
     * @return \DateTime
     */
    public function getDateSigned()
    {
        return $this->dateSigned;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Program\Entity\NdaObject $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return \Program\Entity\NdaObject|null
     */
    public function getObject()
    {
        if ($this->object->count() > 0) {
            return $this->object[0];
        } else {
            return null;
        }
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}

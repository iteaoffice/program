<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Entity for a nda.
 *
 * @ORM\Table(name="nda")
 * @ORM\Entity(repositoryClass="Program\Repository\Nda")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("nda")
 *
 * @category    Contact
 */
class Nda extends AbstractEntity
{
    /**
     * @ORM\Column(name="nda_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $dateApproved;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="ndaApprover")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="approve_contact_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $approver;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    private $dateSigned;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programNna")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=true)
     *
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $size;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="nda")
     * @ORM\JoinTable(name="programcall_nda",
     *      joinColumns={@ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")}
     * )
     *
     * @var \Program\Entity\Call\Call[]|ArrayCollection
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\NdaObject", cascade={"persist","remove"}, mappedBy="nda")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\NdaObject[]|ArrayCollection
     */
    private $object;


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->call = new ArrayCollection();
    }

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
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * ToString.
     *
     * @return string
     */
    public function __toString(): string
    {
        /*
         * Return an empty value when no id is known
         */
        if (\is_null($this->id)) {
            return sprintf("NDA_EMPTY");
        }

        return $this->parseFileName();
    }

    /**
     * Parse a filename.
     *
     * @return string
     */
    public function parseFileName(): string
    {
        if ($this->getCall()->isEmpty()) {
            return sprintf("NDA_SEQ_%s", $this->getContact()->getId());
        }

        return str_replace(' ', '_', sprintf("NDA_%s_SEQ_%s", $this->parseCall(), $this->getContact()->getId()));
    }

    /**
     * @return ArrayCollection|Call\Call[]
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param ArrayCollection|Call\Call[] $call
     * @return Nda
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return null|Call\Call
     */
    public function parseCall(): ?Call\Call
    {
        if (!$this->hasCall()) {
            return null;
        }

        return $this->getCall()->first();
    }

    /**
     * @return bool
     */
    public function hasCall(): bool
    {
        return !$this->getCall()->isEmpty();
    }

    /**
     * @return \General\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param \General\Entity\ContentType $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \DateTime
     */
    public function getDateApproved()
    {
        return $this->dateApproved;
    }

    /**
     * @param \DateTime $dateApproved
     */
    public function setDateApproved($dateApproved)
    {
        $this->dateApproved = $dateApproved;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getApprover(): ?\Contact\Entity\Contact
    {
        return $this->approver;
    }

    /**
     * @param \Contact\Entity\Contact $approver
     * @return Nda
     */
    public function setApprover(\Contact\Entity\Contact $approver): Nda
    {
        $this->approver = $approver;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
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
    public function getDateSigned()
    {
        return $this->dateSigned;
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
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Program\Entity\NdaObject[]|ArrayCollection
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param \Program\Entity\NdaObject[] $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}

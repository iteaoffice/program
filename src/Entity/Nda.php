<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Entity;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\ContentType;
use Program\Entity\Call\Call;
use Laminas\Form\Annotation;

use function sprintf;
use function str_replace;

/**
 * @ORM\Table(name="nda")
 * @ORM\Entity(repositoryClass="Program\Repository\Nda")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Instance("Program\Entity\Nda")
 * @Annotation\Name("nda")
 */
class Nda extends AbstractEntity
{
    /**
     * @ORM\Column(name="nda_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $dateApproved;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="ndaApprover")
     * @ORM\JoinColumn(name="approve_contact_id", referencedColumnName="contact_id")
     *
     * @var Contact
     */
    private $approver;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTime
     */
    private $dateSigned;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programNna")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=true)
     *
     * @var ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     *
     * @var int
     */
    private $size;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="nda")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="nda")
     * @ORM\JoinTable(name="programcall_nda",
     *      joinColumns={@ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")}
     * )
     *
     * @var Call[]|ArrayCollection
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\NdaObject", cascade={"persist","remove"}, mappedBy="nda")
     *
     * @var NdaObject[]|ArrayCollection
     */
    private $object;

    public function __construct()
    {
        $this->call = new ArrayCollection();
        $this->object = new ArrayCollection();
    }

    public function isApproved(): bool
    {
        return null !== $this->dateApproved;
    }

    public function __toString(): string
    {
        if (null === $this->id) {
            return sprintf('NDA_EMPTY');
        }

        return $this->parseFileName();
    }

    public function parseFileName(): string
    {
        if ($this->call->isEmpty()) {
            return sprintf('NDA_SEQ_%s', $this->contact->getId());
        }

        return str_replace(' ', '_', sprintf('NDA_%s_SEQ_%s', $this->parseCall(), $this->contact->getId()));
    }

    public function parseCall(): ?Call
    {
        if (! $this->hasCall()) {
            return null;
        }

        return $this->call->first();
    }

    public function hasCall(): bool
    {
        return ! $this->call->isEmpty();
    }

    public function getCall()
    {
        return $this->call;
    }

    public function setCall($call): Nda
    {
        $this->call = $call;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Nda
    {
        $this->contact = $contact;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDateApproved(): ?DateTime
    {
        return $this->dateApproved;
    }

    public function setDateApproved(?DateTime $dateApproved): Nda
    {
        $this->dateApproved = $dateApproved;
        return $this;
    }

    public function getApprover(): ?Contact
    {
        return $this->approver;
    }

    public function setApprover(?Contact $approver): Nda
    {
        $this->approver = $approver;
        return $this;
    }

    public function getDateSigned(): ?DateTime
    {
        return $this->dateSigned;
    }

    public function setDateSigned(?DateTime $dateSigned): Nda
    {
        $this->dateSigned = $dateSigned;
        return $this;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(ContentType $contentType): Nda
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): Nda
    {
        $this->size = $size;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Nda
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Nda
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): Nda
    {
        $this->object = $object;
        return $this;
    }
}

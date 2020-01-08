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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="program_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("program_doa")
 *
 * @category    Program
 */
class Doa extends AbstractEntity
{
    /**
     * @ORM\Column(name="doa_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $dateApproved;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     *
     * @var \DateTime
     */
    private $dateSigned;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programDoa")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\File")
     * @Annotation\Options({"label":"txt-nda-file"})
     *
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\Column(name="size", type="integer", options={"unsigned":true})
     *
     * @var int
     */
    private $size;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\DoaObject", cascade={"persist","remove"}, mappedBy="doa")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\DoaObject[]|ArrayCollection
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="programDoa")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="programDoa")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     *
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="doa")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     *
     * @var \Program\Entity\Program
     */
    private $program;

    public function __construct()
    {
        $this->object = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('Doa: %s', $this->id);
    }

    public function parseFileName(): string
    {
        return str_replace(' ', '_', sprintf('DOA_%s_%s', $this->getOrganisation(), $this->getProgram()));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Doa
    {
        $this->id = $id;
        return $this;
    }

    public function getDateApproved(): ?\DateTime
    {
        return $this->dateApproved;
    }

    public function setDateApproved(?\DateTime $dateApproved): Doa
    {
        $this->dateApproved = $dateApproved;
        return $this;
    }

    public function getDateSigned(): ?\DateTime
    {
        return $this->dateSigned;
    }

    public function setDateSigned(?\DateTime $dateSigned): Doa
    {
        $this->dateSigned = $dateSigned;
        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): Doa
    {
        $this->branch = $branch;
        return $this;
    }

    public function getContentType(): ?\General\Entity\ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?\General\Entity\ContentType $contentType): Doa
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): Doa
    {
        $this->size = $size;
        return $this;
    }

    public function getDateUpdated(): ?\DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?\DateTime $dateUpdated): Doa
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): Doa
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): Doa
    {
        $this->object = $object;
        return $this;
    }

    public function getContact(): ?\Contact\Entity\Contact
    {
        return $this->contact;
    }

    public function setContact(?\Contact\Entity\Contact $contact): Doa
    {
        $this->contact = $contact;
        return $this;
    }

    public function getOrganisation(): ?\Organisation\Entity\Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?\Organisation\Entity\Organisation $organisation): Doa
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): Doa
    {
        $this->program = $program;
        return $this;
    }
}

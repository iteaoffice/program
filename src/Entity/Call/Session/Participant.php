<?php

/**
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Entity\Call\Session;

use Contact\Entity\Contact;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;
use Program\Entity\Call\Session;
use Program\Entity\AbstractEntity;

/**
 * @ORM\Table(name="programcall_session_participant")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("programcall_session_participant")
 */
class Participant extends AbstractEntity
{
    /**
     * @ORM\Column(name="participant_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     *
     * @var DateTime
     */
    private $lastUpdate;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="callSessionParticipant", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Session", inversedBy="participant",cascade={"persist"})
     * @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var Session
     */
    private $session;

    public function __toString(): string
    {
        return (string)$this->contact;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Participant
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Participant
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?DateTime $lastUpdate): Participant
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Participant
    {
        $this->contact = $contact;
        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): Participant
    {
        $this->session = $session;
        return $this;
    }
}

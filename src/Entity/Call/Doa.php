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

namespace Program\Entity\Call;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\Organisation;
use Program\Entity\AbstractEntity;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="programcall_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_doa")
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
     * @ORM\Column(name="date_received", type="date", nullable=false)
     *
     * @var DateTime
     */
    private $dateReceived;
    /**
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     *
     * @var DateTime
     */
    private $dateSigned;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", cascade="persist", inversedBy="doa")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     *
     * @var Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="doa")
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     *
     * @var Call
     */
    private $call;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Doa
    {
        $this->id = $id;
        return $this;
    }

    public function getDateReceived(): ?DateTime
    {
        return $this->dateReceived;
    }

    public function setDateReceived(?DateTime $dateReceived): Doa
    {
        $this->dateReceived = $dateReceived;
        return $this;
    }

    public function getDateSigned(): ?DateTime
    {
        return $this->dateSigned;
    }

    public function setDateSigned(?DateTime $dateSigned): Doa
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

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): Doa
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getCall(): ?Call
    {
        return $this->call;
    }

    public function setCall(?Call $call): Doa
    {
        $this->call = $call;
        return $this;
    }
}

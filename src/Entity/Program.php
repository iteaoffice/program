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

use Contact\Entity\Dnd;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Invoice\Entity\Method;
use Laminas\Form\Annotation;
use Organisation\Entity\Parent\Invoice;
use Organisation\Entity\Parent\InvoiceExtra;
use Program\Entity\Call\Call;

/**
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Program\Repository\Program")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("contact_contact")
 */
class Program extends AbstractEntity
{
    /**
     * @ORM\Column(name="program_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="program", type="string", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-name-label","help-block":"txt-program-name-help-block"})
     *
     * @var string
     */
    private $program;
    /**
     * @ORM\Column(name="number", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-number-label","help-block":"txt-program-label-help-block"})
     *
     * @var string
     */
    private $number;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Call\Call", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var Call[]
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Doa", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Doa[]
     */
    private $doa;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Doa", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Doa[]|Collections\ArrayCollection
     */
    private $parentDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Dnd", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var Dnd[]
     */
    private $contactDnd;
    /**
     * @ORM\ManyToMany(targetEntity="Invoice\Entity\Method", cascade={"persist"}, inversedBy="program")
     * @ORM\JoinTable(name="invoice_method_program",
     *            joinColumns={@ORM\JoinColumn(name="program_id", referencedColumnName="program_id", unique=true)},
     *            inverseJoinColumns={@ORM\JoinColumn(name="method_id", referencedColumnName="method_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({
     *      "target_class":"Invoice\Entity\Method",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "method":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-program-invoice-method-label","help-block":"txt-program-invoice-method-help-block"})
     *
     * @var Method[]|Collections\ArrayCollection
     */
    private $invoiceMethod;
    /**
     * @ORM\ManyToMany(targetEntity="Cluster\Entity\Cluster", cascade={"persist"}, inversedBy="program")
     * @ORM\JoinTable(name="program_cluster",
     *            joinColumns={@ORM\JoinColumn(name="program_id", referencedColumnName="program_id")},
     *            inverseJoinColumns={@ORM\JoinColumn(name="cluster_id", referencedColumnName="cluster_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({
     *      "target_class":"Cluster\Entity\Cluster",
     *      "find_method":{
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "name":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-program-cluster-label","help-block":"txt-program-cluster-help-block"})
     *
     * @var \Cluster\Entity\Cluster[]|Collections\ArrayCollection
     */
    private $cluster;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Invoice", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var Invoice[]|Collections\ArrayCollection
     */
    private $parentInvoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\InvoiceExtra", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var InvoiceExtra[]|Collections\ArrayCollection
     */
    private $parentInvoiceExtra;

    public function __construct()
    {
        $this->call               = new Collections\ArrayCollection();
        $this->cluster            = new Collections\ArrayCollection();
        $this->contactDnd         = new Collections\ArrayCollection();
        $this->doa                = new Collections\ArrayCollection();
        $this->parentDoa          = new Collections\ArrayCollection();
        $this->invoiceMethod      = new Collections\ArrayCollection();
        $this->parentInvoice      = new Collections\ArrayCollection();
        $this->parentInvoiceExtra = new Collections\ArrayCollection();
    }


    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(?string $program): Program
    {
        $this->program = $program;
        return $this;
    }

    public function searchName(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return (string)$this->program;
    }

    public function addCluster(Collections\Collection $clusters): void
    {
        foreach ($clusters as $cluster) {
            $this->cluster->add($cluster);
        }
    }

    public function removeCluster(Collections\Collection $clusters): void
    {
        foreach ($clusters as $cluster) {
            $this->cluster->removeElement($cluster);
        }
    }

    public function addInvoiceMethod(Collections\Collection $invoiceMethodCollection): void
    {
        foreach ($invoiceMethodCollection as $invoiceMethod) {
            $this->invoiceMethod->add($invoiceMethod);
        }
    }

    public function removeInvoiceMethod(Collections\Collection $invoiceMethodCollection): void
    {
        foreach ($invoiceMethodCollection as $single) {
            $this->invoiceMethod->removeElement($single);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Program
    {
        $this->id = $id;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): Program
    {
        $this->number = $number;
        return $this;
    }

    public function getCall()
    {
        return $this->call;
    }

    public function setCall($call): Program
    {
        $this->call = $call;
        return $this;
    }

    public function getDoa()
    {
        return $this->doa;
    }

    public function setDoa($doa): Program
    {
        $this->doa = $doa;
        return $this;
    }

    public function getParentDoa()
    {
        return $this->parentDoa;
    }

    public function setParentDoa($parentDoa): Program
    {
        $this->parentDoa = $parentDoa;
        return $this;
    }

    public function getContactDnd()
    {
        return $this->contactDnd;
    }

    public function setContactDnd($contactDnd): Program
    {
        $this->contactDnd = $contactDnd;
        return $this;
    }

    public function getInvoiceMethod()
    {
        return $this->invoiceMethod;
    }

    public function setInvoiceMethod($invoiceMethod): Program
    {
        $this->invoiceMethod = $invoiceMethod;
        return $this;
    }

    public function getParentInvoice()
    {
        return $this->parentInvoice;
    }

    public function setParentInvoice($parentInvoice): Program
    {
        $this->parentInvoice = $parentInvoice;
        return $this;
    }

    public function getParentInvoiceExtra()
    {
        return $this->parentInvoiceExtra;
    }

    public function setParentInvoiceExtra($parentInvoiceExtra): Program
    {
        $this->parentInvoiceExtra = $parentInvoiceExtra;
        return $this;
    }

    public function getCluster()
    {
        return $this->cluster;
    }

    public function setCluster($cluster): Program
    {
        $this->cluster = $cluster;
        return $this;
    }
}

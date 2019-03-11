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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Program\Repository\Program")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_contact")
 *
 * @category    Program
 */
class Program extends AbstractEntity
{
    /**
     * @ORM\Column(name="program_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="program", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-name-label","help-block":"txt-program-name-help-block"})
     *
     * @var string
     */
    private $program;
    /**
     * @ORM\Column(name="number", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-number-label","help-block":"txt-program-label-help-block"})
     *
     * @var string
     */
    private $number;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Call\Call", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Call[]
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
     * @var \Contact\Entity\Dnd[]
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
     * @var \Invoice\Entity\Method[]|Collections\ArrayCollection
     */
    private $invoiceMethod;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Invoice", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\Invoice[]|Collections\ArrayCollection
     */
    private $parentInvoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\InvoiceExtra", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Organisation\Entity\Parent\InvoiceExtra[]|Collections\ArrayCollection
     */
    private $parentInvoiceExtra;

    public function __construct()
    {
        $this->call = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->parentDoa = new Collections\ArrayCollection();
        $this->invoiceMethod = new Collections\ArrayCollection();
        $this->parentInvoice = new Collections\ArrayCollection();
        $this->parentInvoiceExtra = new Collections\ArrayCollection();
    }

    public function searchName(): string
    {
        $programName = $this->getProgram();

        if (!\is_numeric(\substr($programName, -1))) {
            $programName .= ' 1';
        }

        return $programName;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __isset($property)
    {
        return isset($this->$property);
    }

    public function __toString(): string
    {
        return (string)$this->program;
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

    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param \Program\Entity\Call\Call[] $call
     */
    public function setCall($call)
    {
        $this->call = $call;
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
     * @return string
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param string $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return \Contact\Entity\Dnd[]
     */
    public function getContactDnd()
    {
        return $this->contactDnd;
    }

    /**
     * @param \Contact\Entity\Dnd[] $contactDnd
     */
    public function setContactDnd($contactDnd)
    {
        $this->contactDnd = $contactDnd;
    }

    /**
     * @return Doa[]
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param Doa[] $doa
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Method[]
     */
    public function getInvoiceMethod()
    {
        return $this->invoiceMethod;
    }

    /**
     * @param Collections\ArrayCollection|\Invoice\Entity\Method[] $invoiceMethod
     */
    public function setInvoiceMethod($invoiceMethod)
    {
        $this->invoiceMethod = $invoiceMethod;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Parent\Doa[]
     */
    public function getParentDoa()
    {
        return $this->parentDoa;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\Parent\Doa[] $parentDoa
     *
     * @return Program
     */
    public function setParentDoa($parentDoa)
    {
        $this->parentDoa = $parentDoa;

        return $this;
    }

    /**
     * @return \Organisation\Entity\Parent\Invoice[]|Collections\ArrayCollection
     */
    public function getParentInvoice()
    {
        return $this->parentInvoice;
    }

    /**
     * @param \Organisation\Entity\Parent\Invoice $parentInvoice
     *
     * @return Program
     */
    public function setParentInvoice(\Organisation\Entity\Parent\Invoice $parentInvoice): Program
    {
        $this->parentInvoice = $parentInvoice;

        return $this;
    }

    /**
     * @return \Organisation\Entity\Parent\InvoiceExtra[]|Collections\ArrayCollection
     */
    public function getParentInvoiceExtra()
    {
        return $this->parentInvoiceExtra;
    }

    /**
     * @param \Organisation\Entity\Parent\InvoiceExtra $parentInvoiceExtra
     *
     * @return Program
     */
    public function setParentInvoiceExtra(\Organisation\Entity\Parent\InvoiceExtra $parentInvoiceExtra): Program
    {
        $this->parentInvoiceExtra = $parentInvoiceExtra;

        return $this;
    }
}

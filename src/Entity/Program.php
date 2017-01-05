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

namespace Program\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Program\Repository\Program")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_contact")
 *
 * @category    Program
 */
class Program extends EntityAbstract implements ResourceInterface
{
    /**
     * @ORM\Column(name="program_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="program", type="string", length=10, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-name-label","help-block":"txt-program-name-help-block"})
     *
     * @var string
     */
    private $program;
    /**
     * @ORM\Column(name="number", type="string", length=10, nullable=true)
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
     * Class constructor.
     */
    public function __construct()
    {
        $this->call          = new Collections\ArrayCollection();
        $this->doa           = new Collections\ArrayCollection();
        $this->parentDoa     = new Collections\ArrayCollection();
        $this->invoiceMethod = new Collections\ArrayCollection();
    }

    /**
     * Magic Getter.
     *
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
     * @param $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * toString returns the name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->program;
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $invoiceMethodCollection
     */
    public function addInvoiceMethod(Collections\Collection $invoiceMethodCollection)
    {
        foreach ($invoiceMethodCollection as $invoiceMethod) {
            $this->invoiceMethod->add($invoiceMethod);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $invoiceMethodCollection
     */
    public function removeInvoiceMethod(Collections\Collection $invoiceMethodCollection)
    {
        foreach ($invoiceMethodCollection as $single) {
            $this->invoiceMethod->removeElement($single);
        }
    }

    /**
     * @return \Program\Entity\Call\Call[]|Collections\ArrayCollection
     *
     */
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
}

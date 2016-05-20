<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Program\Repository\Program")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
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
     * @Annotation\Options({"label":"txt-program-name"})
     *
     * @var string
     */
    private $program;
    /**
     * @ORM\Column(name="number", type="string", length=10, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-number"})
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
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Dnd", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Dnd[]
     */
    private $contactDnd;
    /**
     * @ORM\ManyToMany(targetEntity="Invoice\Entity\Method", cascade={"persist"}, mappedBy="program")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Method[]|Collections\ArrayCollection
     */
    private $invoiceMethod;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->call = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->invoiceMethod = new Collections\ArrayCollection();
    }

    /**
     * Returns the string identifier of the Resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return sprintf("%s:%s", __CLASS__, $this->id);
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
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * toString returns the name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->program;
    }

    /**
     * Set input filter.
     *
     * @param InputFilterInterface $inputFilter
     *
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'call' => $this->call,
        ];
    }

    /**
     * @return \Program\Entity\Call\Call[]
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
     * @param string $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
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
}

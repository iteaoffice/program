<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Project
 * @package    Entity
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Entity for a nda
 *
 * @ORM\Table(name="nda")
 * @ORM\Entity(repositoryClass="Program\Repository\Nda")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("nda")
 *
 * @category    Contact
 * @package     Entity
 */
class Nda extends EntityAbstract implements ResourceInterface
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
     * @ORM\ManyToMany(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="nda")
     * @ORM\JoinTable(name="programcall_nda",
     *      joinColumns={@ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Call\Call"})
     * @Annotation\Attributes({"label":"txt-program-call"})
     * @var \Program\Entity\Call\Call[]|ArrayCollection
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\NdaObject", cascade={"persist"}, mappedBy="nda")
     * @Annotation\Exclude()
     * @var \Program\Entity\NdaObject[]
     */
    private $object;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->version = new ArrayCollection();
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
        /**
         * Return an empty value when no id is known
         */
        if (is_null($this->id)) {
            return sprintf("NDA_EMPTY");
        }

        return $this->parseFileName();
    }

    /**
     * Parse a filename
     *
     * @return string
     */
    public function parseFileName()
    {
        if (is_null($this->getCall())) {
            return sprintf("NDA_SEQ_%s", $this->getContact()->getId());
        }

        return str_replace(' ', '_', sprintf("NDA_%s_SEQ_%s", $this->getCall(), $this->getContact()->getId()));
    }

    /**
     * @return null|\Program\Entity\Call\Call
     */
    public function getCall()
    {
        if (is_null($this->call)) {
            return null;
        }

        return $this->call->first();
    }

    /**
     * @param \Program\Entity\Call\Call[]|ArrayCollection[] $call
     */
    public function setCall($call)
    {
        $this->call = $call;
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
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return sprintf("%s:%s", __CLASS__, $this->id);
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
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
        ];
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
            $factory = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'dateApproved',
                        'required' => false,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'contact',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'call',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'program',
                        'required' => true,
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
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
     * @return \Program\Entity\NdaObject[]
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

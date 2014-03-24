<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\FileInput;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="program_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("program_doa")
 *
 * @category    Program
 * @package     Entity
 */
class ProgramDoa extends EntityAbstract
{
    /**
     * @ORM\Column(name="doa_id", type="integer", nullable=false)
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
     * @var \DateTime
     */
    private $dateSigned;
    /**
     * @ORM\Column(name="branch", type="string", length=40, nullable=true)
     * @var string
     */
    private $branch;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programDoa")
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
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\ProgramDoaObject", cascade={"persist","remove"},
     * mappedBy="programDoa", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     * @var \Program\Entity\ProgramDoaObject[]
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     * @var \Organisation\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     * @var \Program\Entity\Program
     */
    private $program;

    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * Magic Getter
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
     * Magic Setter
     *
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
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
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
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'dateApproved',
                    'required' => false,
                )));

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'dateSigned',
                    'required' => false,
                )));

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'branch',
                    'required' => false,
                )));

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'contact',
                    'required' => false,
                )));

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'organisation',
                    'required' => false,
                )));

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'program',
                    'required' => false,
                )));

            $fileUpload = new FileInput('file');
            $fileUpload->setRequired(true);
            $fileUpload->getValidatorChain()->attachByName(
                'File\Size',
                array(
                    'min' => '20kB',
                    'max' => '8MB',
                )
            );

            $inputFilter->add($fileUpload);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \General\Entity\ContentType $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \General\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
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
    public function getDateApproved()
    {
        return $this->dateApproved;
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
    public function getDateCreated()
    {
        return $this->dateCreated;
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
    public function getDateSigned()
    {
        return $this->dateSigned;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Organisation\Entity\Organisation $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Program\Entity\Program $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param \Program\Entity\ProgramDoaObject[] $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return \Program\Entity\ProgramDoaObject[]
     */
    public function getObject()
    {
        return $this->object;
    }
}

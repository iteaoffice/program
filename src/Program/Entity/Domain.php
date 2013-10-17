<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity;

use Program\Entity\EntityAbstract;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectDomain
 *
 * @ORM\Table(name="domain")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("project_domain")
 * @ORM\Entity
 */
class Domain extends EntityAbstract
{
    /**
     * @ORM\Column(name="domain_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="domain", type="string", length=45, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-domain"})
     * @Annotation\Attributes({"required":"true","class":"span3"})
     * @Annotation\Required(true)
     * @var string
     */
    private $domain;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-description"})
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="color", type="string", length=6, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-color"})
     * @var string
     */
    private $color;
    /**
     * @ORM\Column(name="main_id", type="integer", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-mian_id"})
     * @var integer
     */
    private $mainId;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", cascade={"persist"}, inversedBy="domain")
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id", nullable=TRUE)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Roadmap"})
     * @Annotation\Attributes({"label":"txt-roadmap"})
     * @var \Program\Entity\Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Project", mappedBy="domain")
     * @var \Project\Entity\Project[]
     */
    private $project;
    /**
     * @ORM\ManyToMany(targetEntity="Organisation\Entity\Organisation", mappedBy="domain")
     * @var \Organisation\Entity\Organisation[]
     */
    private $organisation;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", mappedBy="domain")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Contact[]
     */
    private $contact;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->project      = new Collections\ArrayCollection();
        $this->organisation = new Collections\ArrayCollection();
        $this->contact      = new Collections\ArrayCollection();
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
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return __NAMESPACE__ . ':' . __CLASS__ . ':' . $this->id;
    }

    /**
     * ToString
     * @return string
     */
    public function __toString()
    {
        return $this->domain;
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
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'domain',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'description',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'color',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'mainId',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'roadmap',
                        'required' => true,
                    )
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
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
     * @param int $mainId
     */
    public function setMainId($mainId)
    {
        $this->mainId = $mainId;
    }

    /**
     * @return int
     */
    public function getMainId()
    {
        return $this->mainId;
    }

    /**
     * @param \Program\Entity\Roadmap $roadmap
     */
    public function setRoadmap($roadmap)
    {
        $this->roadmap = $roadmap;
    }

    /**
     * @return \Program\Entity\Roadmap
     */
    public function getRoadmap()
    {
        return $this->roadmap;
    }

    /**
     * @param \Contact\Entity\Contact[] $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact[]
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Project\Entity\Project[] $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Project\Entity\Project[]
     */
    public function getProject()
    {
        return $this->project;
    }
}
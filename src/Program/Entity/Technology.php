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

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Technology
 *
 * @ORM\Table(name="technology")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("technology")
 *
 * @category    Program
 * @package     Entity
 */
class Technology extends EntityAbstract
{
    /**
     * @ORM\Column(name="technology_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="technology", type="string", length=45, nullable=false)
     * Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-technology"})
     * @Annotation\Required(true)
     * @var string
     */
    private $technology;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-description"})
     * @Annotation\Required(true)
     * @var string
     */
    private $description;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", cascade={"persist"}, inversedBy="technology")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Roadmap"})
     * @Annotation\Attributes({"label":"txt-roadmap", "required":"true","class":"span3"})
     * @var \Program\Entity\Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", cascade={"persist"}, mappedBy="technology")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
    /**
     * @ORM\ManyToMany(targetEntity="Organisation\Entity\Organisation", cascade={"persist"}, mappedBy="technology")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Organisation[]
     */
    private $organisation;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Project", cascade={"persist"}, mappedBy="technology")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $project;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="technology")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Idea[]
     */
    private $idea;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->contact = new Collections\ArrayCollection();
        $this->organisation = new Collections\ArrayCollection();
        $this->project = new Collections\ArrayCollection();
        $this->idea = new Collections\ArrayCollection();
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
        return (string) $this->technology;
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
                        'name'       => 'technology',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 255,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'description',
                        'required' => true,
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'roadmap',
                        'required' => true,
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @return array
     */
    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'technology'  => $this->technology,
            'roadmap'     => $this->roadmap,
            'description' => $this->description,
        ];
    }

    /**
     * @return \Contact\Entity\Contact[]
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact[] $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return \Organisation\Entity\Organisation[]
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Organisation\Entity\Organisation[] $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \Project\Entity\Project[]
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Project\Entity\Project[] $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Program\Entity\Roadmap
     */
    public function getRoadmap()
    {
        return $this->roadmap;
    }

    /**
     * @param \Program\Entity\Roadmap $roadmap
     */
    public function setRoadmap($roadmap)
    {
        $this->roadmap = $roadmap;
    }

    /**
     * @return string
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param string $technology
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
    }

    /**
     * @return \Project\Entity\Idea\Idea[]
     */
    public function getIdea()
    {
        return $this->idea;
    }

    /**
     * @param \Project\Entity\Idea\Idea[] $idea
     */
    public function setIdea($idea)
    {
        $this->idea = $idea;
    }
}

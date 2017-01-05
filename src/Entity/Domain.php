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
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * ProjectDomain.
 *
 * @ORM\Table(name="domain")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("project_domain")
 * @ORM\Entity
 */
class Domain extends EntityAbstract
{
    /**
     * @ORM\Column(name="domain_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="domain", type="string", length=45, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-domain"})
     * @Annotation\Attributes({"required":"true","class":"span3"})
     * @Annotation\Required(true)
     *
     * @var string
     */
    private $domain;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-description"})
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="color", type="string", length=6, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-color"})
     *
     * @var string
     */
    private $color;
    /**
     * @ORM\Column(name="main_id", length=10, type="integer", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-mian_id"})
     *
     * @var integer
     */
    private $mainId;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", cascade={"persist"}, inversedBy="domain")
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id", nullable=TRUE)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Roadmap"})
     * @Annotation\Attributes({"label":"txt-roadmap"})
     *
     * @var \Program\Entity\Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Project", mappedBy="domain")
     *
     * @var \Project\Entity\Project[]
     */
    private $project;
    /**
     * @ORM\ManyToMany(targetEntity="Organisation\Entity\Organisation", mappedBy="domain")
     *
     * @var \Organisation\Entity\Organisation[]
     */
    private $organisation;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", mappedBy="domain")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="domain")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\Idea[]
     */
    private $idea;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->project      = new Collections\ArrayCollection();
        $this->organisation = new Collections\ArrayCollection();
        $this->contact      = new Collections\ArrayCollection();
        $this->idea         = new Collections\ArrayCollection();
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
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Returns the string identifier of the Resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return __NAMESPACE__ . ':' . __CLASS__ . ':' . $this->id;
    }

    /**
     * ToString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->domain;
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
            'id'           => $this->id,
            'domain'       => $this->domain,
            'description'  => $this->description,
            'color'        => $this->color,
            'mainId'       => $this->mainId,
            'roadmap'      => $this->roadmap,
            'project'      => $this->project,
            'organisation' => $this->organisation,
            'contact'      => $this->contact,
        ];
    }

    /**
     * @param InputFilterInterface $inputFilter
     *
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
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'domain',
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
                                    'max'      => 100,
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
                        'required' => false,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'color',
                        'required' => false,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'mainId',
                        'required' => false,
                        'filters'  => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
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
     * @return string
     */
    public function getColor()
    {
        return $this->color;
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
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
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
     * @return int
     */
    public function getMainId()
    {
        return $this->mainId;
    }

    /**
     * @param int $mainId
     */
    public function setMainId($mainId)
    {
        $this->mainId = $mainId;
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
}

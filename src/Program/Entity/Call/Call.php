<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Project
 * @package    Entity
 * @subpackage Call
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Entity\Call;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Program\Entity\EntityAbstract;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Table(name="programcall")
 * @ORM\Entity(repositoryClass="Program\Repository\Call\Call")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("program_call")
 *
 * @category    Program
 * @package     Entity
 */
class Call extends EntityAbstract implements ResourceInterface
{
    /**
     * Produce a list of different statuses in a call, which are required for representation and access control
     */
    const FPP_CLOSED   = 'FPP_CLOSED';
    const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    const FPP_OPEN     = 'FPP_OPEN';
    const PO_CLOSED    = 'PO_CLOSED';
    const PO_NOT_OPEN  = 'PO_NOT_OPEN';
    const PO_OPEN      = 'PO_OPEN';
    /**
     * @ORM\Column(name="programcall_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="programcall", type="string", length=5, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-call"})
     * @var string
     */
    private $call;
    /**
     * @ORM\Column(name="po_open_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-po-open-date"})
     * @var \DateTime
     */
    private $poOpenDate;
    /**
     * @ORM\Column(name="po_close_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-po-close-date"})
     * @var \DateTime
     */
    private $poCloseDate;
    /**
     * @ORM\Column(name="fpp_open_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-fpp-open-date"})
     * @var \DateTime
     */
    private $fppOpenDate;
    /**
     * @ORM\Column(name="fpp_close_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-fpp-close-date"})
     * @var \DateTime
     */
    private $fppCloseDate;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", cascade={"persist"}, inversedBy="call")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Program"})
     * @Annotation\Attributes({"label":"txt-program", "required":"true","class":"span3"})
     * @var \Program\Entity\Program
     */
    private $program;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $project;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", inversedBy="call")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id")
     * })
     * @var \Program\Entity\Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Nda", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Program\Entity\Nda[]
     */
    private $nda;
    /**
     * @ORM\ManyToMany(targetEntity="\Publication\Entity\Publication", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Publication\Entity\Publication[]
     */
    private $publication;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Meeting", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Event\Entity\Meeting\Meeting[]
     */
    private $meeting;
    /**
     * @ORM\ManyToMany(targetEntity="Calendar\Entity\Calendar", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Calendar\Entity\Calendar[]
     */
    private $calendar;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Doa", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Program\Entity\Call\Doa[]
     */
    private $doa;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Image", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Program\Entity\Call\Image[]
     */
    private $image;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Idea[]
     */
    private $idea;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Session", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     * @var \Program\Entity\Call\Session[]
     */
    private $session;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->publication = new Collections\ArrayCollection();
        $this->meeting     = new Collections\ArrayCollection();
        $this->nda         = new Collections\ArrayCollection();
        $this->calendar    = new Collections\ArrayCollection();
        $this->doa         = new Collections\ArrayCollection();
        $this->image       = new Collections\ArrayCollection();
        $this->idea        = new Collections\ArrayCollection();
        $this->session     = new Collections\ArrayCollection();
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
     * toString returns the name
     *
     * @return string
     */
    public function __toString()
    {
        //return $this->getProgram()->getProgram() . ' Call ' . $this->call;
        return $this->call;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param \Program\Entity\Program $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
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
                $factory->createInput(
                    array(
                        'name'       => 'call',
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
                        'name'       => 'poOpenDate',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'poCloseDate',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'fppOpenDate',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'fppCloseDate',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Date',
                            ),
                        ),
                    )
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'call',
                        'required' => true,
                    )
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
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
        return array(
            'call'         => $this->call,
            'project'      => $this->project,
            'poOpenDate'   => $this->poOpenDate,
            'poCloseDate'  => $this->poCloseDate,
            'fppOpenDate'  => $this->fppOpenDate,
            'fppCloseDate' => $this->fppCloseDate,
            'nda'          => $this->nda,
        );
    }

    /**
     * @return string
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param string $call
     */
    public function setCall($call)
    {
        $this->call = $call;
    }

    /**
     * @return \DateTime
     */
    public function getFppCloseDate()
    {
        return $this->fppCloseDate;
    }

    /**
     * @param \DateTime $fppCloseDate
     */
    public function setFppCloseDate($fppCloseDate)
    {
        $this->fppCloseDate = $fppCloseDate;
    }

    /**
     * @return \DateTime
     */
    public function getFppOpenDate()
    {
        return $this->fppOpenDate;
    }

    /**
     * @param \DateTime $fppOpenDate
     */
    public function setFppOpenDate($fppOpenDate)
    {
        $this->fppOpenDate = $fppOpenDate;
    }

    /**
     * @return \DateTime
     */
    public function getPoCloseDate()
    {
        return $this->poCloseDate;
    }

    /**
     * @param \DateTime $poCloseDate
     */
    public function setPoCloseDate($poCloseDate)
    {
        $this->poCloseDate = $poCloseDate;
    }

    /**
     * @return \DateTime
     */
    public function getPoOpenDate()
    {
        return $this->poOpenDate;
    }

    /**
     * @param \DateTime $poOpenDate
     */
    public function setPoOpenDate($poOpenDate)
    {
        $this->poOpenDate = $poOpenDate;
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
     * @return \Program\Entity\Nda[]
     */
    public function getNda()
    {
        return $this->nda;
    }

    /**
     * @param \Program\Entity\Nda[] $nda
     */
    public function setNda($nda)
    {
        $this->nda = $nda;
    }

    /**
     * @return \Event\Entity\Meeting\Meeting[]
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param \Event\Entity\Meeting\Meeting[] $meeting
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * @return \Publication\Entity\Publication[]
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param \Publication\Entity\Publication[] $publication
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
    }

    /**
     * @return \Calendar\Entity\Calendar[]
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param \Calendar\Entity\Calendar[] $calendar
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return \Program\Entity\Call\Doa[]
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param \Program\Entity\Call\Doa[] $doa
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;
    }

    /**
     * @return \Program\Entity\Call\Image[]
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \Program\Entity\Call\Image[] $image
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * @return \Program\Entity\Call\Session[]
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param \Program\Entity\Call\Session[] $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }
}

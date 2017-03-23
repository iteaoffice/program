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

namespace Program\Entity\Call;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Program\Entity\EntityAbstract;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Table(name="programcall")
 * @ORM\Entity(repositoryClass="Program\Repository\Call\Call")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("program_call")
 *
 * @category    Program
 */
class Call extends EntityAbstract implements ResourceInterface
{
    /**
     * Produce a list of different statuses in a call, which are required for representation and access control.
     */
    const FPP_CLOSED = 'FPP_CLOSED';
    const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    const FPP_GRACE_PERIOD = 'FPP_GRACE_PERIOD';
    const FPP_OPEN = 'FPP_OPEN';
    const PO_CLOSED = 'PO_CLOSED';
    const PO_NOT_OPEN = 'PO_NOT_OPEN';
    const PO_GRACE_PERIOD = 'PO_GRACE_PERIOD';
    const PO_OPEN = 'PO_OPEN';

    const INACTIVE = 0;
    const ACTIVE = 1;
    const DOA_REQUIREMENT_NOT_APPLICABLE = 1;
    const DOA_REQUIREMENT_PER_PROGRAM = 2;
    const DOA_REQUIREMENT_PER_PROJECT = 3;
    const NDA_REQUIREMENT_NOT_APPLICABLE = 1;
    const NDA_REQUIREMENT_PER_CALL = 2;
    const NDA_REQUIREMENT_PER_PROJECT = 3;
    const LOI_NOI_REQUIRED = 0;
    const LOI_REQUIRED = 1;

    /**
     * @var array
     */
    protected static $activeTemplates
        = [
            self::INACTIVE => 'txt-inactive-for-projects',
            self::ACTIVE   => 'txt-active-for-projects',
        ];
    /**
     * @var array
     */
    protected static $doaRequirementTemplates
        = [
            self::DOA_REQUIREMENT_NOT_APPLICABLE => 'txt-no-doa-required',
            self::DOA_REQUIREMENT_PER_PROGRAM    => 'txt-doa-per-program-required',
            self::DOA_REQUIREMENT_PER_PROJECT    => 'txt-doa-per-project-required',
        ];
    /**
     * @var array
     */
    protected static $ndaRequirementTemplates
        = [
            self::NDA_REQUIREMENT_NOT_APPLICABLE => 'txt-no-dna-required',
            self::NDA_REQUIREMENT_PER_CALL       => 'txt-nda-per-call-required',
            self::NDA_REQUIREMENT_PER_PROJECT    => 'txt-nda-per-project-required',
        ];
    /**
     * @var array
     */
    protected static $loiRequirementTemplates
        = [
            self::LOI_NOI_REQUIRED => 'txt-no-loi-required',
            self::LOI_REQUIRED     => 'txt-loi-required',
        ];

    /**
     * @ORM\Column(name="programcall_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="programcall", type="string", length=5, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-call"})
     *
     * @var string
     */
    private $call;
    /**
     * @ORM\Column(name="docref", type="string", length=255, nullable=false, unique=true)
     * @Gedmo\Slug(fields={"call"})
     * @Annotation\Exclude()
     *
     * @var string
     */
    private $docRef;
    /**
     * @ORM\Column(name="po_open_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-po-open-date", "format":"Y-m-d H:i:s"})
     *
     * @var \DateTime
     */
    private $poOpenDate;
    /**
     * @ORM\Column(name="po_close_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-po-close-date", "format":"Y-m-d H:i:s"})
     *
     * @var \DateTime
     */
    private $poCloseDate;
    /**
     * @ORM\Column(name="po_grace_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-po-grace-date", "format":"Y-m-d H:i:s","help-block":"txt-po-grace-date-inline-help"})
     *
     * @var \DateTime
     */
    private $poGraceDate;
    /**
     * @ORM\Column(name="fpp_open_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-fpp-open-date", "format":"Y-m-d H:i:s"})
     *
     * @var \DateTime
     */
    private $fppOpenDate;
    /**
     * @ORM\Column(name="fpp_close_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-fpp-close-date", "format":"Y-m-d H:i:s"})
     *
     * @var \DateTime
     */
    private $fppCloseDate;
    /**
     * @ORM\Column(name="fpp_grace_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-fpp-grace-date", "format":"Y-m-d H:i:s","help-block":"txt-fpp-grace-date-inline-help"})
     *
     * @var \DateTime
     */
    private $fppGraceDate;
    /**
     * @ORM\Column(name="doa_requirement", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"doaRequirementTemplates"})
     * @Annotation\Options({"label":"txt-doa-requirements","help-block":"txt-doa-requirements-inline-help"})
     *
     * @var int
     */
    private $doaRequirement;
    /**
     * @ORM\Column(name="loi_requirement", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"loiRequirementTemplates"})
     * @Annotation\Options({"label":"txt-loi-requirements","help-block":"txt-loi-requirements-inline-help"})
     *
     * @var int
     */
    private $loiRequirement;
    /**
     * @ORM\Column(name="nda_requirement", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"ndaRequirementTemplates"})
     * @Annotation\Options({"label":"txt-nda-requirements","help-block":"txt-nda-requirements-inline-help"})
     *
     * @var int
     */
    private $ndaRequirement;
    /**
     * @ORM\Column(name="active", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"activeTemplates"})
     * @Annotation\Options({"label":"txt-program-call-active-label","help-block":"txt-program-call-active-inline-help"})
     *
     * @var int
     */
    private $active;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", cascade={"persist"}, inversedBy="call")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Program"})
     * @Annotation\Attributes({"label":"txt-program"})
     *
     * @var \Program\Entity\Program
     */
    private $program;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Project[]|Collections\ArrayCollection
     */
    private $project;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", inversedBy="call")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id")
     * })
     *
     * @var \Program\Entity\Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Nda", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Nda[]|Collections\ArrayCollection
     */
    private $nda;
    /**
     * @ORM\ManyToMany(targetEntity="\Publication\Entity\Publication", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Publication\Entity\Publication[]|Collections\ArrayCollection
     */
    private $publication;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Meeting\Meeting", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Meeting\Meeting[]|Collections\ArrayCollection
     */
    private $meeting;
    /**
     * @ORM\ManyToMany(targetEntity="Calendar\Entity\Calendar", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Calendar\Entity\Calendar[]|Collections\ArrayCollection
     */
    private $calendar;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Doa", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Doa[]|Collections\ArrayCollection
     */
    private $doa;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Image", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Image[]|Collections\ArrayCollection
     */
    private $image;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\Idea[]|Collections\ArrayCollection
     */
    private $idea;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Tool", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\Tool[]|Collections\ArrayCollection
     */
    private $ideaTool;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\MessageBoard", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\MessageBoard[]|Collections\ArrayCollection
     */
    private $ideaMessageBoard;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Session", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Session[]|Collections\ArrayCollection
     */
    private $session;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Country", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Country[]|Collections\ArrayCollection
     */
    private $callCountry;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->publication = new Collections\ArrayCollection();
        $this->meeting = new Collections\ArrayCollection();
        $this->project = new Collections\ArrayCollection();
        $this->nda = new Collections\ArrayCollection();
        $this->calendar = new Collections\ArrayCollection();
        $this->doa = new Collections\ArrayCollection();
        $this->image = new Collections\ArrayCollection();
        $this->idea = new Collections\ArrayCollection();
        $this->ideaTool = new Collections\ArrayCollection();
        $this->ideaMessageBoard = new Collections\ArrayCollection();
        $this->session = new Collections\ArrayCollection();
        $this->callCountry = new Collections\ArrayCollection();

        $this->doaRequirement = self::DOA_REQUIREMENT_PER_PROJECT;
        $this->ndaRequirement = self::NDA_REQUIREMENT_PER_CALL;
        $this->loiRequirement = self::LOI_REQUIRED;
        $this->active = self::ACTIVE;
    }

    /**
     * @return array
     */
    public static function getDoaRequirementTemplates(): array
    {
        return self::$doaRequirementTemplates;
    }

    /**
     * @return array
     */
    public static function getNdaRequirementTemplates(): array
    {
        return self::$ndaRequirementTemplates;
    }

    /**
     * @return mixed
     */
    public static function getLoiRequirementTemplates()
    {
        return self::$loiRequirementTemplates;
    }

    /**
     * @return array
     */
    public static function getActiveTemplates(): array
    {
        return self::$activeTemplates;
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
        return sprintf("%s Call %s", $this->getProgram()->getProgram(), $this->call);
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
     * Create a short name.
     *
     * @return string
     */
    public function shortName()
    {
        $words = explode(" ", $this->getProgram()->getProgram());
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= strtoupper($w[0]);
        }

        return sprintf("%sC%s", $acronym, $this->call);
    }

    /**
     * @return string
     */
    public function parseInvoiceName()
    {
        return sprintf("%s %s", $this->call, $this->program->getProgram());
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
     *
     * @return Call
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     *
     * @return Call
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocRef()
    {
        return $this->docRef;
    }

    /**
     * @param string $docRef
     *
     * @return Call
     */
    public function setDocRef($docRef)
    {
        $this->docRef = $docRef;

        return $this;
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
     *
     * @return Call
     */
    public function setPoOpenDate($poOpenDate)
    {
        $this->poOpenDate = $poOpenDate;

        return $this;
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
     *
     * @return Call
     */
    public function setPoCloseDate($poCloseDate)
    {
        $this->poCloseDate = $poCloseDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPoGraceDate()
    {
        return $this->poGraceDate;
    }

    /**
     * @param \DateTime $poGraceDate
     *
     * @return Call
     */
    public function setPoGraceDate($poGraceDate)
    {
        $this->poGraceDate = $poGraceDate;

        return $this;
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
     *
     * @return Call
     */
    public function setFppOpenDate($fppOpenDate)
    {
        $this->fppOpenDate = $fppOpenDate;

        return $this;
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
     *
     * @return Call
     */
    public function setFppCloseDate($fppCloseDate)
    {
        $this->fppCloseDate = $fppCloseDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFppGraceDate()
    {
        return $this->fppGraceDate;
    }

    /**
     * @param \DateTime $fppGraceDate
     *
     * @return Call
     */
    public function setFppGraceDate($fppGraceDate)
    {
        $this->fppGraceDate = $fppGraceDate;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Project[]
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Project[] $project
     *
     * @return Call
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
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
     *
     * @return Call
     */
    public function setRoadmap($roadmap)
    {
        $this->roadmap = $roadmap;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Nda[]
     */
    public function getNda()
    {
        return $this->nda;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Nda[] $nda
     *
     * @return Call
     */
    public function setNda($nda)
    {
        $this->nda = $nda;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Publication\Entity\Publication[]
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param Collections\ArrayCollection|\Publication\Entity\Publication[] $publication
     *
     * @return Call
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Meeting\Meeting[]
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Meeting\Meeting[] $meeting
     *
     * @return Call
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;

        return $this;
    }

    /**
     * @return \Calendar\Entity\Calendar[]|Collections\ArrayCollection
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param \Calendar\Entity\Calendar[]|Collections\ArrayCollection $calendar
     *
     * @return Call
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Doa[]
     */
    public function getDoa()
    {
        return $this->doa;
    }

    /**
     * @param Collections\ArrayCollection|Doa[] $doa
     *
     * @return Call
     */
    public function setDoa($doa)
    {
        $this->doa = $doa;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Image[]
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Collections\ArrayCollection|Image[] $image
     *
     * @return Call
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Idea[]
     */
    public function getIdea()
    {
        return $this->idea;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Idea\Idea[] $idea
     *
     * @return Call
     */
    public function setIdea($idea)
    {
        $this->idea = $idea;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\MessageBoard[]
     */
    public function getIdeaMessageBoard()
    {
        return $this->ideaMessageBoard;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Idea\MessageBoard[] $ideaMessageBoard
     *
     * @return Call
     */
    public function setIdeaMessageBoard($ideaMessageBoard)
    {
        $this->ideaMessageBoard = $ideaMessageBoard;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Session[]
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Collections\ArrayCollection|Session[] $session
     *
     * @return Call
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Country[]
     */
    public function getCallCountry()
    {
        return $this->callCountry;
    }

    /**
     * @param Collections\ArrayCollection|Country[] $callCountry
     *
     * @return Call
     */
    public function setCallCountry($callCountry)
    {
        $this->callCountry = $callCountry;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getNdaRequirement($textual = false)
    {
        if ($textual) {
            return self::$ndaRequirementTemplates[$this->ndaRequirement];
        }

        return $this->ndaRequirement;
    }

    /**
     * @param int $ndaRequirement
     */
    public function setNdaRequirement($ndaRequirement)
    {
        $this->ndaRequirement = $ndaRequirement;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getDoaRequirement($textual = false)
    {
        if ($textual) {
            return self::$doaRequirementTemplates[$this->doaRequirement];
        }

        return $this->doaRequirement;
    }

    /**
     * @param int $doaRequirement
     */
    public function setDoaRequirement($doaRequirement)
    {
        $this->doaRequirement = $doaRequirement;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Tool[]
     */
    public function getIdeaTool()
    {
        return $this->ideaTool;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Idea\Tool[] $ideaTool
     *
     * @return Call
     */
    public function setIdeaTool($ideaTool)
    {
        $this->ideaTool = $ideaTool;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int|string
     */
    public function getActive($textual = false)
    {
        if ($textual) {
            return self::$activeTemplates[$this->active];
        }

        return $this->active;
    }

    /**
     * @param int $active
     *
     * @return Call
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param bool $textual
     * @return int|string
     */
    public function getLoiRequirement(bool $textual = false)
    {
        if ($textual) {
            return self::$loiRequirementTemplates[$this->loiRequirement];
        }

        return $this->loiRequirement;
    }

    /**
     * @param int $loiRequirement
     * @return Call
     */
    public function setLoiRequirement($loiRequirement): Call
    {
        $this->loiRequirement = $loiRequirement;

        return $this;
    }
}

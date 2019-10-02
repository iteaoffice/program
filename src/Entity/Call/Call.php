<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Entity\Call;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Program\Entity\AbstractEntity;
use Program\Entity\Program;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="programcall")
 * @ORM\Entity(repositoryClass="Program\Repository\Call\Call")
 * @Annotation\Name("program_call")
 *
 * @category    Program
 */
class Call extends AbstractEntity
{
    /**
     * Produce a list of different statuses in a call, which are required for representation and access control.
     */
    public const FPP_CLOSED = 'FPP_CLOSED';
    public const FPP_NOT_OPEN = 'FPP_NOT_OPEN';
    public const FPP_OPEN = 'FPP_OPEN';
    public const PO_CLOSED = 'PO_CLOSED';
    public const PO_NOT_OPEN = 'PO_NOT_OPEN';
    public const PO_OPEN = 'PO_OPEN';

    public const INACTIVE = 0;
    public const ACTIVE = 1;
    public const DOA_REQUIREMENT_NOT_APPLICABLE = 1;
    public const DOA_REQUIREMENT_PER_PROGRAM = 2;
    public const DOA_REQUIREMENT_PER_PROJECT = 3;
    public const NDA_REQUIREMENT_NOT_APPLICABLE = 1;
    public const NDA_REQUIREMENT_PER_CALL = 2;
    public const NDA_REQUIREMENT_PER_PROJECT = 3;
    public const LOI_NOI_REQUIRED = 0;
    public const LOI_REQUIRED = 1;

    public const PROJECT_REPORT_SINGLE = 1;
    public const PROJECT_REPORT_DOUBLE = 2;

    protected static $activeTemplates
        = [
            self::INACTIVE => 'txt-inactive-for-projects',
            self::ACTIVE   => 'txt-active-for-projects',
        ];

    protected static $doaRequirementTemplates
        = [
            self::DOA_REQUIREMENT_NOT_APPLICABLE => 'txt-no-doa-required',
            self::DOA_REQUIREMENT_PER_PROGRAM    => 'txt-doa-per-program-required',
            self::DOA_REQUIREMENT_PER_PROJECT    => 'txt-doa-per-project-required',
        ];

    protected static $ndaRequirementTemplates
        = [
            self::NDA_REQUIREMENT_NOT_APPLICABLE => 'txt-no-nda-required',
            self::NDA_REQUIREMENT_PER_CALL       => 'txt-nda-per-call-required',
            self::NDA_REQUIREMENT_PER_PROJECT    => 'txt-nda-per-project-required',
        ];

    protected static $loiRequirementTemplates
        = [
            self::LOI_NOI_REQUIRED => 'txt-no-loi-required',
            self::LOI_REQUIRED     => 'txt-loi-required',
        ];

    protected static $projectReportTemplates
        = [
            self::PROJECT_REPORT_SINGLE => 'txt-project-report-single',
            self::PROJECT_REPORT_DOUBLE => 'txt-project-report-double',
        ];

    /**
     * @ORM\Column(name="programcall_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="programcall", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-program-call"})
     *
     * @var string
     */
    private $call;
    /**
     * @ORM\Column(name="docref", type="string", nullable=true, unique=true)
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
     * @ORM\Column(name="loi_submission_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-loi-submission-date-label", "format":"Y-m-d H:i:s","help-block":"txt-loi-submission-help-block"})
     *
     * @var \DateTime
     */
    private $loiSubmissionDate;
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
     * @ORM\Column(name="doa_submission_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-doa-submission-date-label", "format":"Y-m-d H:i:s","help-block":"txt-doa-submission-help-block"})
     *
     * @var \DateTime
     */
    private $doaSubmissionDate;
    /**
     * @ORM\Column(name="label_announcement_date", type="datetime", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-label-announcement-date-label", "format":"Y-m-d H:i:s","help-block":"txt-label-announcement-help-block"})
     *
     * @var \DateTime
     */
    private $labelAnnouncementDate;
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
     * @ORM\Column(name="project_report", type="smallint", nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"projectReportTemplates"})
     * @Annotation\Options({"label":"txt-call-project-report-label","help-block":"txt-call-project-report-help-block"})
     *
     * @var int
     */
    private $projectReport;
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
     * @ORM\ManyToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="proxyCall")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]|Collections\ArrayCollection
     */
    private $proxyProject;
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
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="call")
     * @ORM\OrderBy({"number" = "ASC"})
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
     * @ORM\ManyToMany(targetEntity="General\Entity\Challenge", cascade={"persist"}, mappedBy="call")
     * @Annotation\Exclude()
     *
     * @var \General\Entity\Challenge[]|Collections\ArrayCollection
     */
    private $challenge;

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
        $this->idea = new Collections\ArrayCollection();
        $this->ideaTool = new Collections\ArrayCollection();
        $this->session = new Collections\ArrayCollection();
        $this->callCountry = new Collections\ArrayCollection();
        $this->challenge = new Collections\ArrayCollection();
        $this->proxyProject = new Collections\ArrayCollection();

        $this->doaRequirement = self::DOA_REQUIREMENT_PER_PROJECT;
        $this->ndaRequirement = self::NDA_REQUIREMENT_PER_CALL;
        $this->loiRequirement = self::LOI_REQUIRED;
        $this->projectReport = self::PROJECT_REPORT_SINGLE;
        $this->active = self::ACTIVE;
    }

    public static function getDoaRequirementTemplates(): array
    {
        return self::$doaRequirementTemplates;
    }

    public static function getNdaRequirementTemplates(): array
    {
        return self::$ndaRequirementTemplates;
    }

    public static function getLoiRequirementTemplates(): array
    {
        return self::$loiRequirementTemplates;
    }

    public static function getActiveTemplates(): array
    {
        return self::$activeTemplates;
    }

    public static function getProjectReportTemplates(): array
    {
        return self::$projectReportTemplates;
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
        return sprintf('%s Call %s', $this->program->getProgram(), $this->call);
    }

    public function shortName(): string
    {
        $words = \explode(' ', $this->program->getProgram());
        $acronym = '';

        foreach ($words as $w) {
            $acronym .= \strtoupper($w[0]);
        }

        return \sprintf('%sC%s', $acronym, $this->call);
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram($program): Call
    {
        $this->program = $program;

        return $this;
    }

    public function searchName(): string
    {
        return \sprintf('%s Call %s', $this->program->searchName(), $this->call);
    }

    public function hasIdeaTool(): bool
    {
        return !$this->ideaTool->isEmpty();
    }

    public function isActive(): bool
    {
        return $this->active === self::ACTIVE;
    }

    public function getProxyProject()
    {
        return $this->proxyProject;
    }

    public function setProxyProject($proxyProject): Call
    {
        $this->proxyProject = $proxyProject;
        return $this;
    }

    public function parseInvoiceName(): string
    {
        return sprintf('%s %s', $this->call, $this->program->getProgram());
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Call
    {
        $this->id = $id;

        return $this;
    }

    public function getCall(): ?string
    {
        return $this->call;
    }

    public function setCall($call): Call
    {
        $this->call = $call;

        return $this;
    }

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef(string $docRef): Call
    {
        $this->docRef = $docRef;

        return $this;
    }

    public function getPoOpenDate(): ?\DateTime
    {
        return $this->poOpenDate;
    }

    public function setPoOpenDate($poOpenDate): Call
    {
        $this->poOpenDate = $poOpenDate;
        return $this;
    }

    public function getPoCloseDate(): ?\DateTime
    {
        return $this->poCloseDate;
    }

    public function setPoCloseDate($poCloseDate): Call
    {
        $this->poCloseDate = $poCloseDate;
        return $this;
    }

    public function getLoiSubmissionDate(): ?\DateTime
    {
        return $this->loiSubmissionDate;
    }

    public function setLoiSubmissionDate($loiSubmissionDate): Call
    {
        $this->loiSubmissionDate = $loiSubmissionDate;
        return $this;
    }

    public function getFppOpenDate()
    {
        return $this->fppOpenDate;
    }

    public function setFppOpenDate($fppOpenDate): Call
    {
        $this->fppOpenDate = $fppOpenDate;
        return $this;
    }

    public function getFppCloseDate()
    {
        return $this->fppCloseDate;
    }

    public function setFppCloseDate($fppCloseDate): Call
    {
        $this->fppCloseDate = $fppCloseDate;
        return $this;
    }

    public function getDoaSubmissionDate()
    {
        return $this->doaSubmissionDate;
    }

    public function setDoaSubmissionDate($doaSubmissionDate): Call
    {
        $this->doaSubmissionDate = $doaSubmissionDate;

        return $this;
    }

    public function getLabelAnnouncementDate()
    {
        return $this->labelAnnouncementDate;
    }

    public function setLabelAnnouncementDate($labelAnnouncementDate): Call
    {
        $this->labelAnnouncementDate = $labelAnnouncementDate;
        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject($project): Call
    {
        $this->project = $project;

        return $this;
    }

    public function getNda()
    {
        return $this->nda;
    }

    public function setNda($nda): Call
    {
        $this->nda = $nda;

        return $this;
    }

    public function getPublication()
    {
        return $this->publication;
    }

    public function setPublication($publication): Call
    {
        $this->publication = $publication;

        return $this;
    }

    public function getMeeting()
    {
        return $this->meeting;
    }

    public function setMeeting($meeting): Call
    {
        $this->meeting = $meeting;

        return $this;
    }

    public function getCalendar()
    {
        return $this->calendar;
    }

    public function setCalendar($calendar): Call
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function getDoa()
    {
        return $this->doa;
    }

    public function setDoa($doa): Call
    {
        $this->doa = $doa;

        return $this;
    }

    public function getIdea()
    {
        return $this->idea;
    }

    public function setIdea($idea): Call
    {
        $this->idea = $idea;

        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession($session): Call
    {
        $this->session = $session;

        return $this;
    }

    public function getCallCountry()
    {
        return $this->callCountry;
    }

    public function setCallCountry($callCountry): Call
    {
        $this->callCountry = $callCountry;

        return $this;
    }

    public function getNdaRequirement(bool $textual = false)
    {
        if ($textual) {
            return self::$ndaRequirementTemplates[$this->ndaRequirement];
        }

        return $this->ndaRequirement;
    }

    public function setNdaRequirement($ndaRequirement): Call
    {
        $this->ndaRequirement = $ndaRequirement;

        return $this;
    }

    public function getDoaRequirement(bool $textual = false)
    {
        if ($textual) {
            return self::$doaRequirementTemplates[$this->doaRequirement];
        }

        return $this->doaRequirement;
    }

    public function setDoaRequirement($doaRequirement): Call
    {
        $this->doaRequirement = $doaRequirement;

        return $this;
    }

    public function getIdeaTool()
    {
        return $this->ideaTool;
    }

    public function setIdeaTool($ideaTool): Call
    {
        $this->ideaTool = $ideaTool;

        return $this;
    }

    public function getActive(bool $textual = false)
    {
        if ($textual) {
            return self::$activeTemplates[$this->active];
        }

        return $this->active;
    }

    public function setActive($active): Call
    {
        $this->active = $active;

        return $this;
    }

    public function getLoiRequirement(bool $textual = false)
    {
        if ($textual) {
            return self::$loiRequirementTemplates[$this->loiRequirement];
        }

        return $this->loiRequirement;
    }

    public function setLoiRequirement($loiRequirement): Call
    {
        $this->loiRequirement = $loiRequirement;

        return $this;
    }

    public function getProjectReport(bool $textual = false)
    {
        if ($textual) {
            return self::$projectReportTemplates[$this->projectReport];
        }

        return $this->projectReport;
    }

    public function setProjectReport(int $projectReport): Call
    {
        $this->projectReport = $projectReport;

        return $this;
    }

    public function getChallenge()
    {
        return $this->challenge;
    }

    public function setChallenge($challenge): Call
    {
        $this->challenge = $challenge;

        return $this;
    }
}

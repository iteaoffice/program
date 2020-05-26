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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Program\Entity\AbstractEntity;
use Program\Entity\Call\Session\Participant;
use Project\Entity\Idea\Tool;

/**
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity(repositoryClass="Program\Repository\Call\Session")
 */
class Session extends AbstractEntity
{
    public const NOT_OPEN_FOR_REGISTRATION = 0;
    public const OPEN_FOR_REGISTRATION     = 1;

    protected static array $openForRegistrationTemplates = [
        self::NOT_OPEN_FOR_REGISTRATION => 'txt-not-open-for-registration',
        self::OPEN_FOR_REGISTRATION     => 'txt-open-for-registration',
    ];

    /**
     * @ORM\Column(name="session_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="session", type="string", length=50, nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-call-session-session-label","placeholder":"txt-call-session-session-placeholder"})
     * @Annotation\Options({"help-block":"txt-call-session-quota-help-block"})
     *
     * @var string
     */
    private $session;
    /**
     * @ORM\Column(name="quota", type="smallint", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Attributes({"label":"txt-call-session-quota-label"})
     * @Annotation\Options({"help-block":"txt-call-session-quota-help-block"})
     *
     * @var int
     */
    private $quota;
    /**
     * @ORM\Column(name="open_for_registration", type="smallint", nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"openForRegistrationTemplates"})
     * @Annotation\Options({"label":"txt-call-session-open-for-registration-label","help-block":"txt-call-session-open-for-registration-help-block"})
     *
     * @var int
     */
    private $openForRegistration;
    /**
     * @ORM\ManyToOne(targetEntity="Project\Entity\Idea\Tool", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="tool_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *     "help-block":"txt-idea-tool-help-block",
     *     "target_class":"Project\Entity\Idea\Tool",
     *     "find_method": {
     *         "name": "findBy",
     *         "params": {
     *             "criteria": {},
     *             "orderBy": {"id": "DESC"}
     *         }
     *     }
     * })
     * @Annotation\Attributes({"label":"txt-call-session-idea-tool-label"})
     * @var Tool
     */
    private $tool;
    /**
     * @ORM\Column(name="date", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @deprecated
     * @var DateTime
     */
    private $date;
    /**
     * @ORM\Column(name="date_from", type="datetime", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\DateTime")
     * @Annotation\Options({"label":"txt-call-session-date-from-label","help-block": "txt-call-session-date-from-help-block", "format": "Y-m-d H:i"})
     * @Annotation\Attributes({"step":"any"})
     * @var DateTime
     */
    private $dateFrom;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\DateTime")
     * @Annotation\Options({"label":"txt-call-session-date-end-label","help-block": "txt-call-session-date-end-help-block", "format": "Y-m-d H:i"})
     * @Annotation\Attributes({"step":"any"})
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Session", cascade={"persist", "remove"}, mappedBy="session", orphanRemoval=true)
     * @ORM\OrderBy({"schedule" = "ASC"})
     * @Annotation\ComposedObject({
     *     "target_object":"Project\Entity\Idea\Session",
     *     "is_collection":"true"
     * })
     * @Annotation\Options({
     *     "allow_add":"true",
     *     "allow_remove":"true",
     *     "count":0,
     *     "label":"txt-ideas"
     * })
     *
     * @var \Project\Entity\Idea\Session[]|Collection
     */
    private $ideaSession;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Session\Participant", cascade={"persist","remove"}, mappedBy="session")
     * @Annotation\Exclude()
     *
     * @var Participant[]|ArrayCollection
     */
    private $participant;

    public function __construct()
    {
        $this->ideaSession = new ArrayCollection();
        $this->participant = new ArrayCollection();
    }

    public static function getOpenForRegistrationTemplates(): array
    {
        return self::$openForRegistrationTemplates;
    }

    public function hasIdeaSession(): bool
    {
        return ! $this->ideaSession->isEmpty();
    }

    public function hasParticipants(): bool
    {
        return ! $this->participant->isEmpty();
    }

    public function isOpenForRegistration(): bool
    {
        return $this->openForRegistration === self::OPEN_FOR_REGISTRATION;
    }

    public function isOverbooked(): bool
    {
        if (! $this->hasQuota()) {
            return false;
        }

        return $this->participant->count() >= $this->quota;
    }

    public function hasQuota(): bool
    {
        return null !== $this->quota;
    }

    public function parseAmountLeft(): ?int
    {
        if (! $this->hasQuota()) {
            return null;
        }

        return max($this->quota - $this->participant->count(), 0);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): Session
    {
        $this->id = $id;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): Session
    {
        $this->session = $session;

        return $this;
    }

    public function getTool(): ?Tool
    {
        return $this->tool;
    }

    public function setTool($tool): Session
    {
        $this->tool = $tool;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): Session
    {
        $this->date = $date;

        return $this;
    }

    public function getIdeaSession()
    {
        return $this->ideaSession;
    }

    public function setIdeaSession($ideaSession): Session
    {
        $this->ideaSession = $ideaSession;

        return $this;
    }

    public function addIdeaSession(Collection $ideaSessions): void
    {
        foreach ($ideaSessions as $ideaSession) {
            $this->ideaSession->add($ideaSession);
        }
    }

    public function removeIdeaSession(Collection $ideaSessions): void
    {
        foreach ($ideaSessions as $ideaSession) {
            $this->ideaSession->removeElement($ideaSession);
        }
    }

    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?DateTime $dateFrom): Session
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTime $dateEnd): Session
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getOpenForRegistration(): ?int
    {
        return $this->openForRegistration;
    }

    public function setOpenForRegistration(?int $openForRegistration): Session
    {
        $this->openForRegistration = $openForRegistration;
        return $this;
    }

    public function getOpenForRegistrationText(): string
    {
        return self::$openForRegistrationTemplates[$this->openForRegistration] ?? '';
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(?int $quota): Session
    {
        $this->quota = $quota;
        return $this;
    }

    public function getParticipant()
    {
        return $this->participant;
    }

    public function setParticipant($participant): Session
    {
        $this->participant = $participant;
        return $this;
    }
}

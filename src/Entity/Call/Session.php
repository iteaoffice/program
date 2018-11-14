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

declare(strict_types=1);

namespace Program\Entity\Call;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Program\Entity\AbstractEntity;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity(repositoryClass="Program\Repository\Call\Session")
 *
 * @category    Program
 */
class Session extends AbstractEntity
{
    /**
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="session", type="string", length=50, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-session"})
     *
     * @var string
     */
    private $session;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *     "label":"txt-program-call",
     *     "target_class": "Program\Entity\Call\Call",
     *     "find_method": {
     *         "name": "findBy",
     *         "params": {
     *             "criteria": {},
     *             "orderBy": {"id": "DESC"}
     *         }
     *     }
     * })
     *
     * @var \Program\Entity\Call\Call
     */
    private $call;
    /**
     * @ORM\ManyToOne(targetEntity="Project\Entity\Idea\Tool", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="tool_id", nullable=true)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({
     *     "label":"txt-idea-tool",
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
     *
     * @var \Project\Entity\Idea\Tool|null
     */
    private $tool;
    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-date"})
     *
     * @var \DateTime
     */
    private $date;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Track", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinTable(name="programcall_session_track",
     *    joinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="session_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="track_id")}
     * )
     * @Annotation\Exclude()
     *
     * @var \Event\Entity\Track[]|Collection
     */
    private $track;
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
     * Class constructor.
     */
    public function __construct()
    {
        $this->track       = new ArrayCollection();
        $this->ideaSession = new ArrayCollection();
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
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
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
     * @return Session
     */
    public function setId(int $id): Session
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSession(): ?string
    {
        return $this->session;
    }

    /**
     * @param string $session
     * @return Session
     */
    public function setSession(string $session): Session
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Call
     */
    public function getCall(): ?Call
    {
        return $this->call;
    }

    /**
     * @param Call $call
     * @return Session
     */
    public function setCall(Call $call): Session
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return null|\Project\Entity\Idea\Tool
     */
    public function getTool(): ?\Project\Entity\Idea\Tool
    {
        return $this->tool;
    }

    /**
     * @param null|\Project\Entity\Idea\Tool $tool
     * @return Session
     */
    public function setTool($tool): Session
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Session
     */
    public function setDate(\DateTime $date): Session
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|\Event\Entity\Track[]
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Collection|\Event\Entity\Track[] $track
     * @return Session
     */
    public function setTrack($track): Session
    {
        $this->track = $track;

        return $this;
    }

    /**
     * @return Collection|\Project\Entity\Idea\Session[]
     */
    public function getIdeaSession()
    {
        return $this->ideaSession;
    }

    /**
     * @param Collection|\Project\Entity\Idea\Session[] $ideaSession
     * @return Session
     */
    public function setIdeaSession($ideaSession)
    {
        $this->ideaSession = $ideaSession;

        return $this;
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collection $ideaSessions
     * @return void
     */
    public function addIdeaSession(Collection $ideaSessions): void
    {
        foreach ($ideaSessions as $ideaSession) {
            $this->ideaSession->add($ideaSession);
        }
    }

    /**
     * @param Collection $ideaSessions
     * @return void
     */
    public function removeIdeaSession(Collection $ideaSessions): void
    {
        foreach ($ideaSessions as $ideaSession) {
            $this->ideaSession->removeElement($ideaSession);
        }
    }
}

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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Program\Entity\AbstractEntity;
use Project\Entity\Idea\Tool;
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
     * @ORM\Column(name="session_id", type="integer", options={"unsigned":true})
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
     * @var Call
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
     * @var Tool|null
     */
    private $tool;
    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\DateTime")
     * @Annotation\Attributes({"step":"any"})
     * @Annotation\Options({"label":"txt-session-date", "format":"Y-m-d H:i:s"})
     *
     * @var DateTime
     */
    private $date;
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

    public function __construct()
    {
        $this->ideaSession = new ArrayCollection();
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

    public function getCall(): ?Call
    {
        return $this->call;
    }

    public function setCall(Call $call): Session
    {
        $this->call = $call;

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
}

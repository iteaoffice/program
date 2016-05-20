<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Entity\Call;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Program\Entity\EntityAbstract;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_session")
 *
 * @category    Program
 */
class Session extends EntityAbstract
{
    /**
     * @ORM\Column(name="session_id", length=10, type="integer", nullable=false)
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
     * @Annotation\Options({"target_class":"Program\Entity\Call\Call"})
     * @Annotation\Attributes({"label":"txt-program-call"})
     *
     * @var \Program\Entity\Call\Call
     */
    private $call;
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
     * @var \Event\Entity\Track[]|Collections\ArrayCollection
     */
    private $track;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Idea\Session", cascade={"persist"}, mappedBy="session")
     * @ORM\OrderBy({"schedule" = "ASC"})
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Idea\Session[]|Collections\ArrayCollection
     */
    private $ideaSession;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->track = new Collections\ArrayCollection();
        $this->ideaSession = new Collections\ArrayCollection();
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Session
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param string $session
     *
     * @return Session
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param Call $call
     *
     * @return Session
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Session
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Track[]
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Track[] $track
     *
     * @return Session
     */
    public function setTrack($track)
    {
        $this->track = $track;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Session[]
     */
    public function getIdeaSession()
    {
        return $this->ideaSession;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Idea\Session[] $ideaSession
     *
     * @return Session
     */
    public function setIdeaSession($ideaSession)
    {
        $this->ideaSession = $ideaSession;

        return $this;
    }
}

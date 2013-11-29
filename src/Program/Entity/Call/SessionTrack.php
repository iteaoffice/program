<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity\Call;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="programcall_session_track")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_session_track")
 *
 * @category    Program
 * @package     Entity
 */
class SessionTrack
{
    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Session", cascade="persist", inversedBy="track")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", nullable=false)
     * })
     * @var \Program\Entity\Call\Session
     */
    private $session;
    /**
     * @ORM\ManyToOne(targetEntity="\Event\Entity\Track", cascade="persist", inversedBy="sessionTrack")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="track_id", referencedColumnName="track_id", nullable=false)
     * })
     * @var \Event\Entity\Track
     */
    private $track;

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
     * @param \Program\Entity\Call\Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return \Program\Entity\Call\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param \Event\Entity\Track $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return \Event\Entity\Track
     */
    public function getTrack()
    {
        return $this->track;
    }
}

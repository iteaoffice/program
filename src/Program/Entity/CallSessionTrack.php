<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity;

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
class CallSessionTrack
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
     * @var integer
     *
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     */
    private $sessionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="track_id", type="integer", nullable=false)
     */
    private $trackId;
}

<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramcallSessionTrack
 *
 * @ORM\Table(name="programcall_session_track")
 * @ORM\Entity
 */
class ProgramcallSessionTrack
{
    /**
     * @var integer
     *
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $linkId;

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

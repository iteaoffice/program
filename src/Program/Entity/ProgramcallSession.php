<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramcallSession
 *
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity
 */
class ProgramcallSession
{
    /**
     * @var integer
     *
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="session", type="string", length=50, nullable=false)
     */
    private $session;

    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_id", type="integer", nullable=false)
     */
    private $programcallId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;
}

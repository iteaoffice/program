<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramcallCalendar
 *
 * @ORM\Table(name="programcall_calendar")
 * @ORM\Entity
 */
class ProgramcallCalendar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_calendar_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $programcallCalendarId;

    /**
     * @var \Calendar
     *
     * @ORM\ManyToOne(targetEntity="Calendar")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="calendar_id", referencedColumnName="calendar_id")
     * })
     */
    private $calendar;

    /**
     * @var \Programcall
     *
     * @ORM\ManyToOne(targetEntity="Programcall")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")
     * })
     */
    private $programcall;
}

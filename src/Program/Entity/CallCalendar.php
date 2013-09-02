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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="programcall_calendar")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class CallCalendar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_calendar_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @todo
     * @var \Calendar
     *
     * @ORM\ManyToOne(targetEntity="Calendar")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="calendar_id", referencedColumnName="calendar_id")
     * })
     * private $calendar;
     */

    /**
     * @var \Programcall
     *
     * @ORM\ManyToOne(targetEntity="Call")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")
     * })
     */
    private $programcall;
}

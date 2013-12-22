<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Entity\Call;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="programcall_calendar")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class Calendar
{
    /**
     * @ORM\Column(name="programcall_calendar_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Calendar")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="calendar_id", referencedColumnName="calendar_id")
     * })
     * @var \Calendar
     * private $calendar;
     */
    /**
     *
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="calendar")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * })
     * @var \Program\Entity\Call\Call
     */
    private $call;
}

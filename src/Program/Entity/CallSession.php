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
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_session")
 *
 * @category    Program
 * @package     Entity
 */
class CallSession
{
    /**
     * @var integer
     *
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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

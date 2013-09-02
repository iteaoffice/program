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
 * @ORM\Table(name="programcall_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_doa")
 *
 * @category    Contact
 * @package     Entity
 */
class CallDoa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="doa_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_received", type="date", nullable=false)
     */
    private $dateReceived;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_signed", type="date", nullable=true)
     */
    private $dateSigned;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=40, nullable=true)
     */
    private $branch;

    /**
     * @var \Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     */
    private $organisation;

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

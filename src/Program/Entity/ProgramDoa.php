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
 * @ORM\Table(name="program_doa")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("program_doa")
 *
 * @category    Program
 * @package     Entity
 */
class ProgramDoa
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
     * @ORM\Column(name="date_approved", type="datetime", nullable=true)
     */
    private $dateApproved;

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
     * @var integer
     *
     * @ORM\Column(name="contenttype_id", type="integer", nullable=false)
     */
    private $contenttypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=false)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="contact_id", type="integer", nullable=false)
     */
    private $contactId;

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
     * @var \Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     */
    private $program;
}

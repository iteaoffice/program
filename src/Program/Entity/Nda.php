<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity for a DND
 *
 * @ORM\Table(name="nda")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("nda")
 *
 * @category    Contact
 * @package     Entity
 */
class Nda
{
    /**
     * @var integer
     *
     * @ORM\Column(name="nda_id", type="integer", nullable=false)
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
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     */
    private $contact;

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

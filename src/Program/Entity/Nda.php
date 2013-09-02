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
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programNna")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-nda-file"})
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=false)
     */
    private $size;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Call", cascade={"persist"}, inversedBy="nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")
     * })
     * @var Call
     */
    private $call;
    /**
     * @ORM\OneToOne(targetEntity="\Program\Entity\NdaObject", cascade={"persist"}, mappedBy="nda")
     * @Annotation\Exclude()
     * @var \Program\Entity\Nda
     */
    private $object;
}

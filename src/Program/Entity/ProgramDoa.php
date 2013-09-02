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

use Doctrine\Common\Collections;
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
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programDoa")
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
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Organisation", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     * @var \Contact\Entity\Organisation
     */
    private $organisation;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program", inversedBy="programDoa")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     * @var \Program\Entity\Program
     */
    private $program;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->call       = new Collections\ArrayCollection();
        $this->programDoa = new Collections\ArrayCollection();
    }
}

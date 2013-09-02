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

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity for a DND
 *
 * @ORM\Table(name="program_dnd")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_dnd")
 *
 * @category    Contact
 * @package     Entity
 */
class Dnd //extends EntityAbstract implements ResourceInterface
{
    /**
     * @ORM\Column(name="dnd_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\Column(name="size", type="integer", nullable=false)
     * @Annotation\Exclude()
     * @var integer
     */
    private $size;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", cascade={"persist"}, inversedBy="dnd")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\ContentType", cascade={"persist"}, inversedBy="programDnd")
     * @ORM\JoinColumn(name="contenttype_id", referencedColumnName="contenttype_id", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\File")
     * @Annotation\Options({"label":"txt-dnd-file"})
     * @var \General\Entity\ContentType
     */
    private $contentType;
    /**
     * @ORM\OneToOne(targetEntity="\Program\Entity\DndObject", cascade={"persist"}, mappedBy="dnd")
     * @Annotation\Exclude()
     * @var \Program\Entity\Dnd
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Program")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     * @var \Program\Entity\Program
     */
    private $program;
}

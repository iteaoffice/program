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

use Zend\InputFilter\InputFilter;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Technology
 *
 * @ORM\Table(name="technology")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("technology")
 *
 * @category    Program
 * @package     Entity
 */
class Technology
{
    /**
     * @ORM\Column(name="technology_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="technology", type="string", length=45, nullable=false)
     * @var string
     */
    private $technology;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Program\Entity\Roadmap", cascade={"persist"}, inversedBy="technology")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id")
     * })
     * @var \Roadmap
     */
    private $roadmap;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Contact", cascade={"persist"}, mappedBy="technology")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
}

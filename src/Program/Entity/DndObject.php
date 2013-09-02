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
 * @ORM\Table(name="program_dnd_object")
 * @ORM\Entity
 *
 * @category    Program
 * @package     Entity
 */
class DndObject
{
    /**
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="object", type="blob", nullable=false)
     * @var string
     */
    private $object;
    /**
     * @ORM\OneToOne(targetEntity="Program\Entity\Dnd", inversedBy="object")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id")
     * })
     * @var \Program\Entity\Dnd
     */
    private $dnd;
}

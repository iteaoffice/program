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
 * @ORM\Table(name="contact_dnd_object")
 * @ORM\Entity
 *
 * @category    Program
 * @package     Entity
 */
class DndObject
{
    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="blob", nullable=false)
     */
    private $object;

    /**
     * @var \ContactDnd
     *
     * @ORM\ManyToOne(targetEntity="Dnd")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id")
     * })
     */
    private $dnd;
}

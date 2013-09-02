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
 * @ORM\Table(name="programcall_image")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class CallImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_extension", type="string", length=45, nullable=true)
     */
    private $imageExtension;

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

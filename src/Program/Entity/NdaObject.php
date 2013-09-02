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
 * @ORM\Table(name="nda_object")
 * @ORM\Entity
 *
 * @category    Program
 * @package     Entity
 */
class NdaObject
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
     * @ORM\Column(name="object", type="blob", nullable=false)
     * @var string
     */
    private $object;
    /**
     * @ORM\OneToOne(targetEntity="Nda", inversedBy="object")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id")
     * })
     * @var Nda
     */
    private $nda;
}

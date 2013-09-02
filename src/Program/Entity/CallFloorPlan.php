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
 * @ORM\Table(name="programcall_floorplan")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_floorplan")
 *
 * @category    Contact
 * @package     Entity
 */
class CallFloorPlan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_floorplan_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_id", type="integer", nullable=false)
     */
    private $programcallId;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_id", type="integer", nullable=false)
     */
    private $imageId;
}

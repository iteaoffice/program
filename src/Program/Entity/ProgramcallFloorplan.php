<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramcallFloorplan
 *
 * @ORM\Table(name="programcall_floorplan")
 * @ORM\Entity
 */
class ProgramcallFloorplan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_floorplan_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $programcallFloorplanId;

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

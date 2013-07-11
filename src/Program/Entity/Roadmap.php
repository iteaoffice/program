<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * Roadmap
 *
 * @ORM\Table(name="roadmap")
 * @ORM\Entity
 */
class Roadmap
{
    /**
     * @var integer
     *
     * @ORM\Column(name="roadmap_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $roadmapId;

    /**
     * @var string
     *
     * @ORM\Column(name="roadmap", type="string", length=40, nullable=true)
     */
    private $roadmap;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_released", type="date", nullable=true)
     */
    private $dateReleased;
}

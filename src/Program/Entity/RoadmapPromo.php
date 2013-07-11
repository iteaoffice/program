<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * RoadmapPromo
 *
 * @ORM\Table(name="roadmap_promo")
 * @ORM\Entity
 */
class RoadmapPromo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="promo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $promoId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=45, nullable=false)
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(name="key_id", type="integer", nullable=false)
     */
    private $keyId;
}

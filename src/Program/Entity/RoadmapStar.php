<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Program\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoadmapPromo
 * @ORM\Table(name="roadmap_star")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("roadmap_star")
 */
class RoadmapStar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="star_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_id", type="integer", nullable=false)
     */
    private $contactId;
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
    /**
     * @var string
     *
     * @ORM\Column(name="star", type="string", length=60, nullable=true)
     */
    private $star;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     */
    private $dateEnd;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */
    private $dateUpdated;
}

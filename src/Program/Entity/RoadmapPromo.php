<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Project
 * @package    Entity
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * RoadmapPromo
 * @ORM\Table(name="roadmap_promo")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("roadmap_promo")
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
    private $id;
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

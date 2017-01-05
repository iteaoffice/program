<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * RoadmapPromo.
 *
 * @ORM\Table(name="roadmap_star")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("roadmap_star")
 * @deprecated
 */
class RoadmapStar
{
    /**
     * @ORM\Column(name="star_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="contact_id", length=10, type="integer", nullable=false)
     *
     * @var integer
     */
    private $contactId;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="entity", type="string", length=45, nullable=false)
     *
     * @var string
     */
    private $entity;
    /**
     * @ORM\Column(name="key_id", length=10, type="integer", nullable=false)
     *
     * @var integer
     */
    private $keyId;
    /**
     * @ORM\Column(name="star", type="string", length=60, nullable=true)
     *
     * @var string
     */
    private $star;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $dateUpdated;

    /**
     * @return mixed
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @param mixed $contactId
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @param int $keyId
     */
    public function setKeyId($keyId)
    {
        $this->keyId = $keyId;
    }

    /**
     * @return mixed
     */
    public function getStar()
    {
        return $this->star;
    }

    /**
     * @param mixed $star
     */
    public function setStar($star)
    {
        $this->star = $star;
    }
}

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

declare(strict_types=1);

declare(strict_types=1);

namespace Program\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Roadmap.
 *
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("program_roadmap")
 * @ORM\Table(name="roadmap")
 * @ORM\Entity
 */
class Roadmap extends EntityAbstract
{
    /**
     * @ORM\Column(name="roadmap_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="roadmap", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-roadmap"})
     *
     * @var string
     */
    private $roadmap;
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="date_released", type="date", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-date-released"})
     *
     * @var \DateTime
     */
    private $dateReleased;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Domain", cascade={"persist"}, mappedBy="roadmap")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Domain[]|ArrayCollection
     */
    private $domain;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Call\Call", cascade={"persist"}, mappedBy="roadmap")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Call[]|ArrayCollection
     */
    private $call;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Technology", cascade={"persist"}, mappedBy="roadmap")
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Technology[]|ArrayCollection
     */
    private $technology;

    /**
     *
     */
    public function __construct()
    {
        $this->domain = new ArrayCollection();
        $this->call = new ArrayCollection();
        $this->technology = new ArrayCollection();
    }

    /**
     * Magic Getter.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }


    /**
     * toString returns the name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->roadmap;
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
     *
     * @return Roadmap
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoadmap()
    {
        return $this->roadmap;
    }

    /**
     * @param string $roadmap
     *
     * @return Roadmap
     */
    public function setRoadmap($roadmap)
    {
        $this->roadmap = $roadmap;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Roadmap
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateReleased()
    {
        return $this->dateReleased;
    }

    /**
     * @param \DateTime $dateReleased
     *
     * @return Roadmap
     */
    public function setDateReleased($dateReleased)
    {
        $this->dateReleased = $dateReleased;

        return $this;
    }

    /**
     * @return ArrayCollection|Domain[]
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param ArrayCollection|Domain[] $domain
     *
     * @return Roadmap
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return ArrayCollection|Call\Call[]
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param ArrayCollection|Call\Call[] $call
     *
     * @return Roadmap
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return ArrayCollection|Technology[]
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param ArrayCollection|Technology[] $technology
     *
     * @return Roadmap
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;

        return $this;
    }
}

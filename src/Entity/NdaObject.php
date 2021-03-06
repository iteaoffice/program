<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="nda_object")
 * @ORM\Entity
 */
class NdaObject extends AbstractEntity
{
    /**
     * @ORM\Column(name="object_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="object", type="blob", nullable=false)
     *
     * @var string
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Nda", inversedBy="object", cascade={"persist"})
     * @ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id",nullable=false)
     *
     * @var Nda
     */
    private $nda;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): NdaObject
    {
        $this->id = $id;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object): NdaObject
    {
        $this->object = $object;
        return $this;
    }

    public function getNda(): ?Nda
    {
        return $this->nda;
    }

    public function setNda(Nda $nda): NdaObject
    {
        $this->nda = $nda;
        return $this;
    }
}

<?php

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactDndObject
 *
 * @ORM\Table(name="contact_dnd_object")
 * @ORM\Entity
 */
class ContactDndObject
{
    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="blob", nullable=false)
     */
    private $object;

    /**
     * @var \ContactDnd
     *
     * @ORM\ManyToOne(targetEntity="Dnd")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id")
     * })
     */
    private $dnd;
}

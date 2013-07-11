<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * NdaObject
 *
 * @ORM\Table(name="nda_object")
 * @ORM\Entity
 */
class NdaObject
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
     * @var \Nda
     *
     * @ORM\ManyToOne(targetEntity="Nda")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id")
     * })
     */
    private $nda;
}

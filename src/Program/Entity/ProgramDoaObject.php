<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramDoaObject
 *
 * @ORM\Table(name="program_doa_object")
 * @ORM\Entity
 */
class ProgramDoaObject
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
     * @var integer
     *
     * @ORM\Column(name="doa_id", type="integer", nullable=false)
     */
    private $doaId;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="blob", nullable=false)
     */
    private $object;
}

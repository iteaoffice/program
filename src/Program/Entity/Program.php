<?php

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Program
 *
 * @ORM\Table(name="program")
 * @ORM\Entity
 */
class Program
{
    /**
     * @var integer
     *
     * @ORM\Column(name="program_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="program", type="string", length=10, nullable=true)
     */
    private $program;
}

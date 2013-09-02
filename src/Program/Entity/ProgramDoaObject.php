<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="program_doa_object")
 * @ORM\Entity
 *
 * @category    Program
 * @package     Entity
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
    private $id;

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

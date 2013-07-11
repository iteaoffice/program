<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramcallImage
 *
 * @ORM\Table(name="programcall_image")
 * @ORM\Entity
 */
class ProgramcallImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $programcallImageId;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_extension", type="string", length=45, nullable=true)
     */
    private $imageExtension;

    /**
     * @var \Programcall
     *
     * @ORM\ManyToOne(targetEntity="Programcall")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id")
     * })
     */
    private $programcall;
}

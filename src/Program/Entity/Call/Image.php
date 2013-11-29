<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Program\Entity\Call;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="programcall_image")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class Image
{
    /**
     * @ORM\Column(name="programcall_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="image", type="blob", nullable=true)
     * @var resource
     */
    private $image;
    /**
     * @ORM\Column(name="image_extension", type="string", length=45, nullable=true)
     * @var string
     */
    private $imageExtension;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="image")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * })
     * @var \Program\Entity\Call\Call
     */
    private $call;

    /**
     * @param \Program\Entity\Call\Call $call
     */
    public function setCall($call)
    {
        $this->call = $call;
    }

    /**
     * @return \Program\Entity\Call\Call
     */
    public function getCall()
    {
        return $this->call;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param resource $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $imageExtension
     */
    public function setImageExtension($imageExtension)
    {
        $this->imageExtension = $imageExtension;
    }

    /**
     * @return string
     */
    public function getImageExtension()
    {
        return $this->imageExtension;
    }
}

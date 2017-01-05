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

namespace Program\Entity\Call;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="programcall_image")
 * @ORM\Entity
 *
 * @category    Contact
 */
class Image
{
    /**
     * @ORM\Column(name="programcall_image_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="image", type="blob", nullable=true)
     *
     * @var resource
     */
    private $image;
    /**
     * @ORM\Column(name="image_extension", type="string", length=45, nullable=true)
     *
     * @var string
     */
    private $imageExtension;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade="persist", inversedBy="image")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * })
     *
     * @var \Program\Entity\Call\Call
     */
    private $call;

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
     * @return Image
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param resource $image
     *
     * @return Image
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageExtension()
    {
        return $this->imageExtension;
    }

    /**
     * @param string $imageExtension
     *
     * @return Image
     */
    public function setImageExtension($imageExtension)
    {
        $this->imageExtension = $imageExtension;

        return $this;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param Call $call
     *
     * @return Image
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }
}

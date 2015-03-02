<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
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
     * @ORM\Column(name="programcall_image_id", type="integer", nullable=false)
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
     * @return \Program\Entity\Call\Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param \Program\Entity\Call\Call $call
     */
    public function setCall($call)
    {
        $this->call = $call;
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
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     */
    public function setImageExtension($imageExtension)
    {
        $this->imageExtension = $imageExtension;
    }
}

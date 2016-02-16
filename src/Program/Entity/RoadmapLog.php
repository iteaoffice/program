<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * RoadmapLog.
 *
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("roadmap_log")
 * @ORM\Table(name="roadmap_log")
 * @ORM\Entity
 */
class RoadmapLog
{
    /**
     * @ORM\Column(name="log_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="roadmapLog")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="uri", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $uri;
    /**
     * @ORM\Column(name="log", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $log;

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
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
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }
}

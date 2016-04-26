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
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Program.
 *
 * @ORM\Table(name="funder")
 * @ORM\Entity(repositoryClass="Program\Repository\Funder")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("funder")
 *
 * @category    Program
 */
class Funder extends EntityAbstract implements ResourceInterface
{
    /**
     * Constant for hideOnWebsite = 0.
     */
    const HIDE_ON_WEBSITE = 0;
    /**
     * Constant for hideOnWebsite = 1.
     */
    const SHOW_ON_WEBSITE = 1;
    /**
     * Textual versions of the showOnWebsite.
     *
     * @var array
     */
    protected static $showOnWebsiteTemplates
        = [
            self::HIDE_ON_WEBSITE => 'txt-hide-on-website',
            self::SHOW_ON_WEBSITE => 'txt-show-on-website',
        ];
    /**
     * @var integer
     *
     * @ORM\Column(name="funder_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="funder")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Options({"label":"txt-contact"})
     *
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", inversedBy="funder", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     * })
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Country"})
     * @Annotation\Attributes({"label":"txt-country", "required":"true","class":"span3"})
     *
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\Column(name="info_office", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-info-office"})
     * @Annotation\Attributes({"rows":20})
     *
     * @var string
     */
    private $infoOffice;
    /**
     * @ORM\Column(name="info_public", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"txt-info-public"})
     * @Annotation\Attributes({"rows":20})
     *
     * @var string
     */
    private $infoPublic;
    /**
     * @ORM\Column(name="show_on_website",type="smallint",nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showOnWebsiteTemplates"})
     * @Annotation\Attributes({"label":"txt-show-on-website"})
     *
     * @var \int
     */
    private $showOnWebsite;
    /**
     * @ORM\Column(name="position", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Number")
     * @Annotation\Options({"label":"txt-sorting-position"})
     *
     * @var int
     */
    private $position;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->position = 1;
    }

    /**
     * Magic Getter.
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter.
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * toString returns the name.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->contact->getDisplayName();
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return sprintf("%s:%s", __CLASS__, $this->id);
    }

    /**
     * @return array
     */
    public static function getShowOnWebsiteTemplates()
    {
        return self::$showOnWebsiteTemplates;
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
     *
     * @return Funder
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Contact\Entity\Contact $contact
     *
     * @return Funder
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \General\Entity\Country $country
     *
     * @return Funder
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfoOffice()
    {
        return $this->infoOffice;
    }

    /**
     * @param string $infoOffice
     *
     * @return Funder
     */
    public function setInfoOffice($infoOffice)
    {
        $this->infoOffice = $infoOffice;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfoPublic()
    {
        return $this->infoPublic;
    }

    /**
     * @param string $infoPublic
     *
     * @return Funder
     */
    public function setInfoPublic($infoPublic)
    {
        $this->infoPublic = $infoPublic;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int
     */
    public function getShowOnWebsite($textual = false)
    {
        if ($textual) {
            return self::$showOnWebsiteTemplates[$this->showOnWebsite];
        }

        return $this->showOnWebsite;
    }

    /**
     * @param int $showOnWebsite
     */
    public function setShowOnWebsite($showOnWebsite)
    {
        $this->showOnWebsite = $showOnWebsite;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return Funder
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}

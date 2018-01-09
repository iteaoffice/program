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

declare(strict_types=1);

namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Program.
 *
 * @ORM\Table(name="funder")
 * @ORM\Entity(repositoryClass="Program\Repository\Funder")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("funder")
 *
 * @category    Program
 */
class Funder extends EntityAbstract implements ResourceInterface
{
    /**
     * Constant for hideOnWebsite = 0.
     */
    public const HIDE_ON_WEBSITE = 0;
    /**
     * Constant for hideOnWebsite = 1.
     */
    public const SHOW_ON_WEBSITE = 1;
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
     * @ORM\Column(name="funder_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="funder")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @Annotation\Type("Contact\Form\Element\Contact")
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
     * @Annotation\Options({
     *      "help-block":"txt-funder-country-help-block",
     *      "target_class":"General\Entity\Country",
     *      "find_method":{
     *          "name":"findForForm",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{}
     *          }}
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-country"})
     *
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\Column(name="info_office", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Attributes({"rows":20,"label":"txt-funder-info-office-label","placeholder":"txt-funder-info-office-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-info-office-help-block"})
     *
     * @var string
     */
    private $infoOffice;
    /**
     * @ORM\Column(name="info_public", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Textarea")
     * @Annotation\Attributes({"rows":20,"label":"txt-funder-info-public-label","placeholder":"txt-funder-info-public-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-info-public-help-block"})
     *
     * @var string
     */
    private $infoPublic;
    /**
     * @ORM\Column(name="show_on_website",type="smallint",nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showOnWebsiteTemplates"})
     * @Annotation\Attributes({"label":"txt-show-on-website"})
     * @Annotation\Options({"help-block":"txt-funder-show-on-website-help-block"})
     *
     * @var int
     */
    private $showOnWebsite;
    /**
     * @ORM\Column(name="position", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Number")
     * @Annotation\Attributes({"label":"txt-funder-sorting-position-label"})
     * @Annotation\Options({"help-block":"txt-funder-sorting-position-help-block"})
     *
     * @var int
     */
    private $position;
    /**
     * @ORM\Column(name="website",type="text",nullable=true)
     * @Annotation\Type("Zend\Form\Element\Url")
     * @Annotation\Attributes({"label":"txt-funder-website-label","placeholder":"txt-funder-website-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-website-help-block"})
     *
     * @var string
     */
    private $website;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->position = 1;
    }

    /**
     * @return array
     */
    public static function getShowOnWebsiteTemplates(): array
    {
        return self::$showOnWebsiteTemplates;
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
     * @param $property
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * toString returns the name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->contact->getDisplayName();
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
    public function setInfoOffice(string $infoOffice = null)
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
    public function setInfoPublic(string $infoPublic = null)
    {
        $this->infoPublic = $infoPublic;

        return $this;
    }

    /**
     * @param bool $textual
     *
     * @return int
     */
    public function getShowOnWebsite(bool $textual = false)
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

    /**
     * @return string
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return Funder
     */
    public function setWebsite(string $website = null): Funder
    {
        $this->website = $website;

        return $this;
    }
}

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

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Program
 *
 * @ORM\Table(name="funder")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("funder")
 *
 * @category    Program
 * @package     Entity
 */
class Funder
{
    /**
     * Constant for hideOnWebsite = 0
     */
    const HIDE_ON_WEBSITE = 0;
    /**
     * Constant for hideOnWebsite = 1
     */
    const SHOW_ON_WEBSITE = 1;

    /**
     * Textual versions of the showOnWebsite
     *
     * @var array
     */
    protected $showOnWebsiteTemplates = array(
        self::HIDE_ON_WEBSITE => 'txt-hide-on-website',
        self::SHOW_ON_WEBSITE => 'txt-show-on-website',
    );

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
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Contact\Entity\Contact"})
     * @Annotation\Attributes({"label":"txt-contact", "required":"true","class":"span3"})
     * @var \Program\Entity\Program
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
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\Column(name="info_office", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-info-office"})
     * @var string
     */
    private $infoOffice;
    /**
     * @ORM\Column(name="info_public", type="text", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-info-public"})
     * @var string
     */
    private $infoPublic;
    /**
     * @ORM\Column(name="show_on_website",type="smallint",nullable=false)
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showOnWebsiteTemplates"})
     * @Annotation\Attributes({"label":"txt-show-on-website"})
     * @var \int
     */
    private $showOnWebsite;

    /**
     * @return array
     */
    public function getShowOnWebsiteTemplates()
    {
        return $this->showOnWebsiteTemplates;
    }

    /**
     * @param \Program\Entity\Program $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Program\Entity\Program
     */
    public function getContact()
    {
        return $this->contact;
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
     * @param string $infoOffice
     */
    public function setInfoOffice($infoOffice)
    {
        $this->infoOffice = $infoOffice;
    }

    /**
     * @return string
     */
    public function getInfoOffice()
    {
        return $this->infoOffice;
    }

    /**
     * @param string $infoPublic
     */
    public function setInfoPublic($infoPublic)
    {
        $this->infoPublic = $infoPublic;
    }

    /**
     * @return string
     */
    public function getInfoPublic()
    {
        return $this->infoPublic;
    }

    /**
     * @param \General\Entity\Country $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
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
    public function getShowOnWebsite()
    {
        return $this->showOnWebsite;
    }
}

<?php

/**
 * ITEA Office all rights reserved
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Entity;

use Contact\Entity\Contact;
use Doctrine\ORM\Mapping as ORM;
use General\Entity\Country;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="funder")
 * @ORM\Entity(repositoryClass="Program\Repository\Funder")
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("funder")
 */
class Funder extends AbstractEntity
{
    public const HIDE_ON_WEBSITE = 0;
    public const SHOW_ON_WEBSITE = 1;

    protected static array $showOnWebsiteTemplates
        = [
            self::HIDE_ON_WEBSITE => 'txt-hide-on-website',
            self::SHOW_ON_WEBSITE => 'txt-show-on-website',
        ];
    /**
     * @var int
     *
     * @ORM\Column(name="funder_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="funder")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * @Annotation\Type("Contact\Form\Element\Contact")
     * @Annotation\Options({"label":"txt-contact"})
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", inversedBy="funder", cascade={"persist"})
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
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
     * @var Country
     */
    private $country;
    /**
     * @ORM\Column(name="info_office", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Attributes({"rows":20,"label":"txt-funder-info-office-label","placeholder":"txt-funder-info-office-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-info-office-help-block"})
     *
     * @var string
     */
    private $infoOffice;
    /**
     * @ORM\Column(name="info_public", type="text", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Textarea")
     * @Annotation\Attributes({"rows":20,"label":"txt-funder-info-public-label","placeholder":"txt-funder-info-public-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-info-public-help-block"})
     *
     * @var string
     */
    private $infoPublic;
    /**
     * @ORM\Column(name="show_on_website",type="smallint",nullable=false)
     * @Annotation\Type("Laminas\Form\Element\Radio")
     * @Annotation\Attributes({"array":"showOnWebsiteTemplates"})
     * @Annotation\Attributes({"label":"txt-show-on-website"})
     * @Annotation\Options({"help-block":"txt-funder-show-on-website-help-block"})
     *
     * @var int
     */
    private $showOnWebsite;
    /**
     * @ORM\Column(name="position", type="smallint", nullable=false)
     * @Annotation\Type("\Laminas\Form\Element\Number")
     * @Annotation\Attributes({"label":"txt-funder-sorting-position-label"})
     * @Annotation\Options({"help-block":"txt-funder-sorting-position-help-block"})
     *
     * @var int
     */
    private $position;
    /**
     * @ORM\Column(name="website",type="text",nullable=true)
     * @Annotation\Type("Laminas\Form\Element\Url")
     * @Annotation\Attributes({"label":"txt-funder-website-label","placeholder":"txt-funder-website-placeholder"})
     * @Annotation\Options({"help-block":"txt-funder-website-help-block"})
     *
     * @var string
     */
    private $website;

    public function __construct()
    {
        $this->position = 1;
        $this->showOnWebsite = self::SHOW_ON_WEBSITE;
    }

    public static function getShowOnWebsiteTemplates(): array
    {
        return self::$showOnWebsiteTemplates;
    }

    public function __toString(): string
    {
        return (string)$this->contact->getDisplayName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Funder
    {
        $this->id = $id;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Funder
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): Funder
    {
        $this->country = $country;
        return $this;
    }

    public function getInfoOffice(): ?string
    {
        return $this->infoOffice;
    }

    public function setInfoOffice(?string $infoOffice): Funder
    {
        $this->infoOffice = $infoOffice;
        return $this;
    }

    public function getInfoPublic(): ?string
    {
        return $this->infoPublic;
    }

    public function setInfoPublic(?string $infoPublic): Funder
    {
        $this->infoPublic = $infoPublic;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): Funder
    {
        $this->position = $position;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Funder
    {
        $this->website = $website;
        return $this;
    }

    public function getShowOnWebsite(bool $textual = false)
    {
        if ($textual) {
            return self::$showOnWebsiteTemplates[$this->showOnWebsite];
        }

        return $this->showOnWebsite;
    }

    public function setShowOnWebsite(?int $showOnWebsite): Funder
    {
        $this->showOnWebsite = $showOnWebsite;
        return $this;
    }
}

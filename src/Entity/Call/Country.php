<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Entity\Call;

use Doctrine\ORM\Mapping as ORM;
use Program\Entity\AbstractEntity;
use Laminas\Form\Annotation;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Table(name="programcall_country")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectPropertyHydrator")
 * @Annotation\Name("programcall_session")
 *
 * @category    Program
 */
class Country extends AbstractEntity
{
    /**
     * @ORM\Column(name="programcall_country_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade={"persist"}, inversedBy="callCountry")
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var \Program\Entity\Call\Call
     */
    private $call;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", cascade={"persist"}, inversedBy="callCountry")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id", nullable=false)
     * @Annotation\Exclude()
     *
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\Column(name="date_national_application", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-date-national-application-label","help-block":"txt-date-national-application-help-block"})
     *
     * @var string
     */
    private $dateNationalApplication;
    /**
     * @ORM\Column(name="date_expected_funding_decision", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-date-expected-funding-decision-label","help-block":"txt-date-expected-funding-decision-help-block"})
     *
     * @var string
     */
    private $dateExpectedFundingDecision;
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getCountry();
    }

    /**
     * @return \General\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return Country
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
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
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Country
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateNationalApplication()
    {
        return $this->dateNationalApplication;
    }

    /**
     * @param string $dateNationalApplication
     *
     * @return Country
     */
    public function setDateNationalApplication($dateNationalApplication)
    {
        $this->dateNationalApplication = $dateNationalApplication;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateExpectedFundingDecision()
    {
        return $this->dateExpectedFundingDecision;
    }

    /**
     * @param string $dateExpectedFundingDecision
     *
     * @return Country
     */
    public function setDateExpectedFundingDecision($dateExpectedFundingDecision)
    {
        $this->dateExpectedFundingDecision = $dateExpectedFundingDecision;

        return $this;
    }
}

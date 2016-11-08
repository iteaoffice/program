<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Entity\Call;

use Doctrine\ORM\Mapping as ORM;
use Program\Entity\EntityAbstract;
use Zend\Form\Annotation;

/**
 * @ORM\Table(name="programcall_country")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_session")
 *
 * @category    Program
 */
class Country extends EntityAbstract
{
    /**
     * @ORM\Column(name="programcall_country_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     *
     * @var integer
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
     * @var \Program\Entity\Country
     */
    private $country;
    /**
     * @ORM\Column(name="date_national_application", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-date-national-application-label","help-block":"txt-date-national-application-help-block"})
     *
     * @var string
     */
    private $dateNationalApplication;
    /**
     * @ORM\Column(name="date_expected_funding_decision", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-date-expected-funding-decision-label","help-block":"txt-date-expected-funding-decision-help-block"})
     *
     * @var string
     */
    private $dateExpectedFundingDecision;

    /**
     * Class constructor.
     */
    public function __construct()
    {
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
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getCountry();
    }

    /**
     * @return Country
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

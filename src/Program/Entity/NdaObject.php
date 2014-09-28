<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Project
 * @package    Entity
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Table(name="nda_object")
 * @ORM\Entity
 *
 * @category    Program
 * @package     Entity
 */
class NdaObject extends EntityAbstract
{
    /**
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="object", type="blob", nullable=false)
     * @var string
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Nda", inversedBy="object", cascade={"persist"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="nda_id", referencedColumnName="nda_id",nullable=false)
     * })
     * @var Nda
     */
    private $nda;

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * ToString
     * @return string
     */
    public function __toString()
    {
        return $this->domain;
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'object' => $this->object,
            'nda'    => $this->nda,
        ];
    }

    /**
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception(sprintf("This class %s is unused", __CLASS__));
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
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
     * @return \Program\Entity\Nda
     */
    public function getNda()
    {
        return $this->nda;
    }

    /**
     * @param \Program\Entity\Nda $nda
     */
    public function setNda($nda)
    {
        $this->nda = $nda;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
}

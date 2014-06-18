<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Project
 * @package    Entity
 * @subpackage Call
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Entity\Call;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Program\Entity\EntityAbstract;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Table(name="programcall_session")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("programcall_session")
 *
 * @category    Program
 * @package     Entity
 */
class Session extends EntityAbstract
{
    /**
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(name="session", type="string", length=50, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-session"})
     * @var string
     */
    private $session;
    /**
     * @ORM\ManyToOne(targetEntity="Program\Entity\Call\Call", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinColumn(name="programcall_id", referencedColumnName="programcall_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"Program\Entity\Call\Call"})
     * @Annotation\Attributes({"label":"txt-program-call", "required":"true","class":"span3"})
     * @var \Program\Entity\Call\Call
     */
    private $call;
    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-date"})
     * @var \DateTime
     */
    private $date;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Track", cascade={"persist"}, inversedBy="session")
     * @ORM\JoinTable(name="programcall_session_track",
     *    joinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="session_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="track_id", referencedColumnName="track_id")}
     * )
     * @Annotation\Exclude()
     * @var \Event\Entity\Track[]
     */
    private $track;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Idea\Session", cascade={"persist"}, mappedBy="session")
     * @ORM\OrderBy({"schedule" = "ASC"})
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Session[]
     */
    private $ideaSession;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->track       = new Collections\ArrayCollection();
        $this->ideaSession = new Collections\ArrayCollection();
    }

    /**
     * Magic Getter
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
     * Magic Setter
     *
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
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'session',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 45,
                                ),
                            ),
                        ),
                    )
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * @return \Project\Entity\Idea\Session[]
     */
    public function getIdeaSession()
    {
        return $this->ideaSession;
    }

    /**
     * @param \Project\Entity\Idea\Session[] $ideaSession
     */
    public function setIdeaSession($ideaSession)
    {
        $this->ideaSession = $ideaSession;
    }

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param string $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return \Program\Entity\Call\SessionTrack[]
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param \Program\Entity\Call\SessionTrack[] $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }
}

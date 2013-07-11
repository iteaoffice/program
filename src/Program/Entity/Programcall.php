<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * Programcall
 *
 * @ORM\Table(name="programcall")
 * @ORM\Entity
 */
class Programcall
{
    /**
     * @var integer
     *
     * @ORM\Column(name="programcall_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $programcallId;

    /**
     * @var string
     *
     * @ORM\Column(name="programcall", type="string", length=5, nullable=true)
     */
    private $programcall;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="po_open_date", type="datetime", nullable=true)
     */
    private $poOpenDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="po_close_date", type="datetime", nullable=true)
     */
    private $poCloseDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fpp_open_date", type="datetime", nullable=true)
     */
    private $fppOpenDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fpp_close_date", type="datetime", nullable=true)
     */
    private $fppCloseDate;

    /**
     * @var \Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="program_id", referencedColumnName="program_id")
     * })
     */
    private $program;

    /**
     * @var \Roadmap
     *
     * @ORM\ManyToOne(targetEntity="Roadmap")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="roadmap_id", referencedColumnName="roadmap_id")
     * })
     */
    private $roadmap;
}

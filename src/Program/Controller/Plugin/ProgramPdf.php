<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Controller\Plugin;


/**
 * Class PDF
 * @package Program\Controller\Plugin
 */
class ProgramPdf extends \FPDI
{
    /**
     * "Remembers" the template id of the imported page
     */
    protected $_tplIdx;
    /**
     * @var Location of the template
     */
    protected $template;

    /**
     * Draw an imported PDF logo on every page
     */
    public function Header()
    {
        if (is_null($this->_tplIdx)) {

            if (!file_exists($this->template)) {
                throw new \InvalidArgumentException(sprintf("Template %s cannot be found", $this->template));
            }

            $this->setSourceFile($this->template);
            $this->_tplIdx = $this->importPage(1);
        }
        $size = $this->useTemplate($this->_tplIdx, 0, 0);

        $this->SetFont('freesans', 'N', 15);
        $this->SetTextColor(0);
        $this->SetXY(PDF_MARGIN_LEFT, 5);
    }

    public function Footer()
    {
        // emtpy method body
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}


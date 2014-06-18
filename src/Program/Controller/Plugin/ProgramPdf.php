<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Controller\Plugin;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Controller
 * @subpackage Plugin
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class ProgramPdf extends \FPDI
{
    /**
     * "Remembers" the template id of the imported page
     */
    protected $_tplIdx;
    /**
     * @var string Location of the template
     */
    protected $template;

    /**
     * Draw an imported PDF logo on every page
     */
    public function header()
    {
        if (is_null($this->_tplIdx)) {
            if (!file_exists($this->template)) {
                throw new \InvalidArgumentException(sprintf("Template %s cannot be found", $this->template));
            }
            $this->setSourceFile($this->template);
            $this->_tplIdx = $this->importPage(1);
        }
        $this->useTemplate($this->_tplIdx, 0, 0);
        $this->SetFont('freesans', 'N', 15);
        $this->SetTextColor(0);
        $this->SetXY(15, 5);
    }

    public function footer()
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

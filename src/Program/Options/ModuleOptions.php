<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    Options
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Options
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class ModuleOptions extends AbstractOptions implements ProgramOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    /**
     * Location of the PDF having the NDA template
     *
     * @var string
     */
    protected $ndaTemplate = '';
    /**
     * Boolean to turn the NDA functionality on or off
     *
     * @var bool
     */
    protected $hasNda = true;
    /**
     * Location of the PDF having the DOA template
     *
     * @var string
     */
    protected $doaTemplate = '';
    /**
     * How program calls to be displayed
     * @var String
     */
    protected $displayName = 'name';

    /**
     * @return string
     */
    public function getNdaTemplate()
    {
        return $this->ndaTemplate;
    }

    /**
     * @param $ndaTemplate
     *
     * @return ModuleOptions
     */
    public function setNdaTemplate($ndaTemplate)
    {
        $this->ndaTemplate = $ndaTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getHasNda()
    {
        return $this->hasNda;
    }

    /**
     * @param $hasNda
     *
     * @return ModuleOptions
     */
    public function setHasNda($hasNda)
    {
        $this->hasNda = $hasNda;

        return $this;
    }

    /**
     * @param $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return String
     */
    public function getDisplayName()
    {
        return $this->displayName;

    }

    /**
     * @return string
     */
    public function getDoaTemplate()
    {
        return $this->doaTemplate;
    }

    /**
     * @param $doaTemplate
     *
     * @return ModuleOptions
     */
    public function setDoaTemplate($doaTemplate)
    {
        $this->doaTemplate = $doaTemplate;

        return $this;
    }
}

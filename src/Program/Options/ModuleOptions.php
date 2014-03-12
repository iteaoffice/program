<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Options
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    ProgramOptionsInterface
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
    public function getNdaTemplate()
    {
        return $this->ndaTemplate;
    }
}

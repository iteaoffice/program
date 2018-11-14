<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 * @package Program\Options
 */
class ModuleOptions extends AbstractOptions implements ProgramOptionsInterface
{
    /**
     * Turn off strict options mode.
     */
    protected $__strictMode__ = false;
    /**
     * Location of the PDF having the NDA template.
     *
     * @var string
     */
    protected $ndaTemplate = '';

    /**
     * Boolean to turn the NDA functionality on or off.
     *
     * @var bool
     */
    protected $hasNda = true;

    /**
     * Location of the PDF having the DOA template.
     *
     * @var string
     */
    protected $doaTemplate = '';

    /**
     * Location of the PDF having the DOA template.
     *
     * @var string
     */
    protected $blankTemplate = '';
    /**
     * How program calls to be displayed.
     *
     * @var String
     */
    protected $displayName = 'name';

    /**
     * Header logo for Word documents
     *
     * @var string
     */
    protected $headerLogo = '';

    /**
     * Footer image for Word documents
     *
     * @var string
     */
    protected $footerImage = '';

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
     * @return String
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param $displayName
     *
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
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

    /**
     * @return string
     */
    public function getBlankTemplate()
    {
        return $this->blankTemplate;
    }

    /**
     * @param $blankTemplate
     *
     * @return ModuleOptions
     */
    public function setBlankTemplate($blankTemplate)
    {
        $this->blankTemplate = $blankTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderLogo(): string
    {
        return $this->headerLogo;
    }

    /**
     * @param string $headerLogo
     * @return ModuleOptions
     */
    public function setHeaderLogo(string $headerLogo): ModuleOptions
    {
        $this->headerLogo = $headerLogo;
        return $this;
    }

    /**
     * @return string
     */
    public function getFooterImage(): string
    {
        return $this->footerImage;
    }

    /**
     * @param string $footerImage
     * @return ModuleOptions
     */
    public function setFooterImage(string $footerImage): ModuleOptions
    {
        $this->footerImage = $footerImage;
        return $this;
    }
}

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
 *
 * @package Program\Options
 */
class ModuleOptions extends AbstractOptions implements ProgramOptionsInterface
{
    protected $__strictMode__ = false;

    protected $ndaTemplate = '';

    protected $hasNda = true;

    protected $doaTemplate = '';

    protected $blankTemplate = '';

    protected $headerLogo = '';

    protected $footerImage = '';

    public function getNdaTemplate(): string
    {
        return $this->ndaTemplate;
    }

    public function setNdaTemplate(string $ndaTemplate): ModuleOptions
    {
        $this->ndaTemplate = $ndaTemplate;
        return $this;
    }

    public function getHasNda(): bool
    {
        return $this->hasNda;
    }

    public function setHasNda(bool $hasNda): ModuleOptions
    {
        $this->hasNda = $hasNda;
        return $this;
    }

    public function getDoaTemplate(): string
    {
        return $this->doaTemplate;
    }

    public function setDoaTemplate(string $doaTemplate): ModuleOptions
    {
        $this->doaTemplate = $doaTemplate;
        return $this;
    }

    public function getBlankTemplate(): string
    {
        return $this->blankTemplate;
    }

    public function setBlankTemplate(string $blankTemplate): ModuleOptions
    {
        $this->blankTemplate = $blankTemplate;
        return $this;
    }

    public function getHeaderLogo(): string
    {
        return $this->headerLogo;
    }

    public function setHeaderLogo(string $headerLogo): ModuleOptions
    {
        $this->headerLogo = $headerLogo;
        return $this;
    }

    public function getFooterImage(): string
    {
        return $this->footerImage;
    }

    public function setFooterImage(string $footerImage): ModuleOptions
    {
        $this->footerImage = $footerImage;
        return $this;
    }
}

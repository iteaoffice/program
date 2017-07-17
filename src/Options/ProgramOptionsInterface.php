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

/**
 * Interface ProgramOptionsInterface
 * @package Program\Options
 */
interface ProgramOptionsInterface
{
    /**
     * @param $ndaTemplate
     *
     * @return ProgramOptionsInterface
     */
    public function setNdaTemplate($ndaTemplate);

    /**
     * @return bool
     */
    public function getNdaTemplate();

    /**
     * @param $hasNda
     *
     * @return ProgramOptionsInterface
     */
    public function setHasNda($hasNda);

    /**
     * @return bool
     */
    public function getHasNda();

    /**
     * @param $doaTemplate
     *
     * @return ProgramOptionsInterface
     */
    public function setDoaTemplate($doaTemplate);

    /**
     * @return string
     */
    public function getDoaTemplate();

    /**
     * @param $displayName
     *
     * @return ProgramOptionsInterface
     */
    public function setDisplayName($displayName);

    /**
     * @return string
     */
    public function getDisplayName();
}

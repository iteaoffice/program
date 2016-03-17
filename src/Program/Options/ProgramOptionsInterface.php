<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Options;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
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

    /**
     * @param $countryColor
     *
     * @return ModuleOptions
     */
    public function setCountryColor($countryColor);

    /**
     * @return string
     */
    public function getCountryColor();

    /**
     * Returns the assigned hex color of the country map.
     *
     * @param string $countryColorFaded
     *
     * @return ModuleOptions
     */
    public function setCountryColorFaded($countryColorFaded);

    /**
     * Returns the assigned hex color of the country map.
     *
     * @return string
     */
    public function getCountryColorFaded();

    /**
     * Returns the assigned hex color of the country map.
     *
     * @param string $requireMembership
     *
     * @return ModuleOptions
     */
    public function setRequireMembership($requireMembership);

    /**
     * Returns the assigned hex color of the country map.
     *
     * @return string
     */
    public function getRequireMembership();
}

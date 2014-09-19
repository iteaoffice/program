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

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Options
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
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
     * @return boolean
     */
    public function getNdaTemplate();

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
     * @return ProgramOptionsInterface
     */
    public function setDisplayName($displayName);

    /**
     * @return string
     */
    public function getDisplayName();
}

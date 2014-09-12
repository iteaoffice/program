<?php
/**
 * ITEA Office copyright message placeholder
 *
 * PHP Version 5
 *
 * @category    Project
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2014 ITEA Office
 * @license     http://debranova.org/license.txt proprietary
 * @link        http://debranova.org
 */
namespace Program\Service;

use Program\Options\ModuleOptions;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Service
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
interface ModuleOptionAwareInterface
{
    /**
     * Get config.
     *
     * @return ModuleOptions.
     */
    public function getOptions();

    /**
     * Set options.
     *
     * @param ModuleOptions $options the value to set.
     *
     * @return $this
     */
    public function setOptions(ModuleOptions $options);
}

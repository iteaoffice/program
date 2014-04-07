<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Options
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Options;

/**
 * Interface ProgramOptionsInterface
 *
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
}

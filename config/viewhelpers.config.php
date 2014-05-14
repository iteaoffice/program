<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

return array(
    'factories'  => array(),
    'invokables' => array(
        'programHandler'      => 'Program\View\Helper\ProgramHandler',
        'callServiceProxy'    => 'Program\View\Helper\CallServiceProxy',
        'programServiceProxy' => 'Program\View\Helper\ProgramServiceProxy',
        'callInformationBox'  => 'Program\View\Helper\CallInformationBox',
        'programLink'         => 'Program\View\Helper\ProgramLink',
        'programDoaLink'      => 'Program\View\Helper\DoaLink',
        'callLink'            => 'Program\View\Helper\CallLink',
        'ndaLink'             => 'Program\View\Helper\NdaLink',
    )
);

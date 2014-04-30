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
    'factories'  => array(
        'programHandler' => 'Program\View\Helper\Service\ProgramHandlerFactory',
        'programLink'    => 'Program\View\Helper\Service\ProgramLinkFactory',
        'programDoaLink' => 'Program\View\Helper\Service\DoaLinkFactory',
        'callLink'       => 'Program\View\Helper\Service\CallLinkFactory',
        'ndaLink'        => 'Program\View\Helper\Service\NdaLinkFactory',
    ),
    'invokables' => array(
        'callServiceProxy'    => 'Program\View\Helper\CallServiceProxy',
        'programServiceProxy' => 'Program\View\Helper\ProgramServiceProxy',
        'callInformationBox'  => 'Program\View\Helper\CallInformationBox',
    )
);

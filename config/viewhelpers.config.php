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
        'programHandler'      => function ($sm) {
            return new \Program\View\Helper\ProgramHandler($sm);
        },
        'callServiceProxy'    => function ($sm) {
            return new \Program\View\Helper\CallServiceProxy($sm);
        },
        'programServiceProxy' => function ($sm) {
            return new \Program\View\Helper\ProgramServiceProxy($sm);
        },
    ),
    'invokables' => array(
        'programLink'        => 'Program\View\Helper\ProgramLink',
        'programDoaLink'     => 'Program\View\Helper\DoaLink',
        'callLink'           => 'Program\View\Helper\CallLink',
        'callInformationBox' => 'Program\View\Helper\CallInformationBox',
        'ndaLink'            => 'Program\View\Helper\NdaLink',
        'domainLink'         => 'Program\View\Helper\DomainLink',
        'technologyLink'     => 'Program\View\Helper\TechnologyLink',
    )
);

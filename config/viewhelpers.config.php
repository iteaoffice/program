<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */

return array(
    'factories'  => array(
        'programHandler'      => function ($sm) {
                return new \Program\View\Helper\ProgramHandler($sm);
            },
        'programServiceProxy' => function ($sm) {
                return new \Program\View\Helper\ProgramServiceProxy($sm);
            },
    ),
    'invokables' => array(
        'programLink'    => 'Program\View\Helper\ProgramLink',
        'callLink'       => 'Program\View\Helper\CallLink',
        'ndaLink'        => 'Program\View\Helper\NdaLink',
        'domainLink'     => 'Program\View\Helper\DomainLink',
        'technologyLink' => 'Program\View\Helper\TechnologyLink',
    )
);

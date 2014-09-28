<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Admin
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return [
    'router' => [
        'routes' => [
            'route-program_entity_program' => [
                'type'     => 'Literal',
                'priority' => 1000,
                'options'  => [
                    'route'    => '/community',
                    'defaults' => [
                        'controller' => 'admin',
                        'action'     => 'community',
                    ],
                ],
            ],
            'route-content_entity_node'    => [
                'type'     => 'Literal',
                'priority' => 1000,
                'options'  => [
                    'route'    => '/community',
                    'defaults' => [
                        'controller' => 'admin',
                        'action'     => 'community',
                    ],
                ],
            ],
        ],
    ],
];

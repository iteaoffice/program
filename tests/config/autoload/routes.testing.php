<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Admin
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return array(
    'router' => array(
        'routes' => array(
            'route-program_entity_program' => array(
                'type'     => 'Literal',
                'priority' => 1000,
                'options'  => array(
                    'route'    => '/community',
                    'defaults' => array(
                        'controller' => 'admin',
                        'action'     => 'community',
                    ),
                ),
            ),
            'route-content_entity_node'    => array(
                'type'     => 'Literal',
                'priority' => 1000,
                'options'  => array(
                    'route'    => '/community',
                    'defaults' => array(
                        'controller' => 'admin',
                        'action'     => 'community',
                    ),
                ),
            ),
        ),
    ),
);

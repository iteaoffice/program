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
    'router'       => array(
        'routes' => array(
            'program_shortcut' => array(
                'type'     => 'Segment',
                'priority' => -1000,
                'options'  => array(
                    'route'       => 'l/:id',
                    'constraints' => array(
                        'id' => '\d+',
                    ),
                    'defaults'    => array(
                        'controller' => 'program',
                        'action'     => 'programRedirect',
                    ),
                ),
            ),
            'program'          => array(
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => array(
                    'route'    => '/program',
                    'defaults' => array(
                        'controller' => 'program',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'programs' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/programs.html',
                            'defaults' => array(
                                'action' => 'programs',
                            ),
                        ),
                    ),
                    'program'  => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/program/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'program',
                            ),
                        ),
                    ),
                    'call'     => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '/call/[:id].html',
                            'constraints' => array(
                                'id' => '\d+',
                            ),
                            'defaults'    => array(
                                'action' => 'call',
                            ),
                        ),
                    ),
                    'calls'    => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/calls.html',
                            'defaults' => array(
                                'action' => 'calls',
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin'         => array(
                'child_routes' => array(
                    'program-manager' => array(
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => array(
                            'route'    => '/program-manager',
                            'defaults' => array(
                                'controller' => 'program-manager',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(

                            'new'    => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/new/:entity',
                                    'defaults' => array(
                                        'action' => 'new',
                                    ),
                                ),
                            ),
                            'edit'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/edit/:entity/:id',
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/delete/:entity/:id',
                                    'defaults' => array(
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
);

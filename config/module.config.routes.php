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
    'router' => array(
        'routes' => array(
            'program'  => array(
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
                    'nda' => array(
                        'type'         => 'Literal',
                        'priority'     => 1000,
                        'options'      => array(
                            'route'    => '/nda',
                            'defaults' => array(
                                'controller' => 'program-nda',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' => array(
                            'view-call'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/view/call-[:call].html',
                                    'constraints' => array(
                                        'call' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action' => 'view-call',
                                    ),
                                ),
                            ),
                            'render-call' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/render/call-[:call].pdf',
                                    'constraints' => array(
                                        'call' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action' => 'render-call',
                                    ),
                                ),
                            ),
                            'render'      => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/render/nda.pdf',
                                    'defaults' => array(
                                        'action' => 'render',
                                    ),
                                ),
                            ),
                            'view'        => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/view.html',
                                    'defaults' => array(
                                        'action' => 'view',
                                    ),
                                ),
                            ),
                            'download'    => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/download/nda-[:id].pdf',
                                    'constraints' => array(
                                        'id' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action' => 'download',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin' => array(
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
);

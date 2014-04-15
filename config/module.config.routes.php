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
                            'upload'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/upload[/call-:call-id].html',
                                    'constraints' => array(
                                        'call-id' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action'    => 'upload',
                                        'privilege' => 'upload'
                                    ),
                                ),
                            ),
                            'render'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/render[/call-:call-id].pdf',
                                    'constraints' => array(
                                        'call-id' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action'    => 'render',
                                        'privilege' => 'render'
                                    ),
                                ),
                            ),
                            'view'     => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/view/nda-[:id].html',
                                    'defaults' => array(
                                        'action'    => 'view',
                                        'privilege' => 'view'
                                    ),
                                ),
                            ),
                            'replace'  => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/replace/nda-[:id].html',
                                    'constraints' => array(
                                        'id' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action'    => 'replace',
                                        'privilege' => 'replace'
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'       => '/download/nda-[:id].pdf',
                                    'constraints' => array(
                                        'id' => '\d+'
                                    ),
                                    'defaults'    => array(
                                        'action'    => 'download',
                                        'privilege' => 'download'
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'doa' => array(
                        'type'         => 'Segment',
                        'options'      => array(
                            'route'    => '/doa',
                            'defaults' => array(
                                'controller' => 'program-doa',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' => array(
                            'render'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/render/organisation-[:organisation-id]/program-[:program-id].pdf',
                                    'defaults' => array(
                                        'action'    => 'render',
                                        'privilege' => 'render'
                                    ),
                                ),
                            ),
                            'upload'   => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/upload/organisation-[:organisation-id]/program-[:program-id].html',
                                    'defaults' => array(
                                        'action'    => 'upload',
                                        'privilege' => 'upload'
                                    ),
                                ),
                            ),
                            'view'     => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/view/[:id].html',
                                    'defaults' => array(
                                        'action'    => 'view',
                                        'privilege' => 'view'
                                    ),
                                ),
                            ),
                            'replace'  => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/replace/[:id].html',
                                    'defaults' => array(
                                        'action'    => 'replace',
                                        'privilege' => 'replace'
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/download/[:id].pdf',
                                    'defaults' => array(
                                        'action'    => 'download',
                                        'privilege' => 'download'
                                    ),
                                ),
                            ),
                        )
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

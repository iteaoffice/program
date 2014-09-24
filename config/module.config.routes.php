<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
return [
    'router' => [
        'routes' => [
            'program'  => [
                'type'          => 'Literal',
                'priority'      => 1000,
                'options'       => [
                    'route'    => '/program',
                    'defaults' => [
                        'namespace'  => 'program',
                        'controller' => 'program',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                        'view' => [
                        'type'         => 'Literal',
                        'priority'     => 1000,
                        'options'      => [
                            'route'    => '/view',
                            'defaults' => [
                                'controller' => 'program',
                                'action'     => 'view',
                            ],
                        ]],
                        'programcall' => [
                            'type'     => 'Segment',
                            'priority' => -1000,
                            'options'  => [
                                'route'       => '/programcall/[:id].html',
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                                'defaults'    => [
                                    'controller' => 'programcall',
                                    'action'     => 'view',
                                ],
                            ],
                        ],
                        'nda' => [
                        'type'         => 'Literal',
                        'priority'     => 1000,
                        'options'      => [
                            'route'    => '/nda',
                            'defaults' => [
                                'controller' => 'program-nda',
                                'action'     => 'index',
                            ],
                        ],
                        'child_routes' => [
                            'upload'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/upload[/call-:id].html',
                                    'constraints' => [
                                        'id' => '\d+'
                                    ],
                                    'defaults'    => [
                                        'action'    => 'upload',
                                        'privilege' => 'upload'
                                    ],
                                ],
                            ],
                            'render'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/render[/call-:id].pdf',
                                    'constraints' => [
                                        'id' => '\d+'
                                    ],
                                    'defaults'    => [
                                        'action'    => 'render',
                                        'privilege' => 'render'
                                    ],
                                ],
                            ],
                            'view'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/nda-[:id].html',
                                    'defaults' => [
                                        'action'    => 'view',
                                        'privilege' => 'view'
                                    ],
                                ],
                            ],
                            'replace'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/replace/nda-[:id].html',
                                    'constraints' => [
                                        'id' => '\d+'
                                    ],
                                    'defaults'    => [
                                        'action'    => 'replace',
                                        'privilege' => 'replace'
                                    ],
                                ],
                            ],
                            'download' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/download/nda-[:id].pdf',
                                    'constraints' => [
                                        'id' => '\d+'
                                    ],
                                    'defaults'    => [
                                        'action'    => 'download',
                                        'privilege' => 'download'
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'doa' => [
                        'type'         => 'Segment',
                        'options'      => [
                            'route'    => '/doa',
                            'defaults' => [
                                'controller' => 'program-doa',
                                'action'     => 'index',
                            ],
                        ],
                        'child_routes' => [
                            'render'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/render/organisation-[:organisation-id]/program-[:program-id].pdf',
                                    'defaults' => [
                                        'action'    => 'render',
                                        'privilege' => 'render'
                                    ],
                                ],
                            ],
                            'upload'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/upload/organisation-[:organisation-id]/program-[:program-id].html',
                                    'defaults' => [
                                        'action'    => 'upload',
                                        'privilege' => 'upload'
                                    ],
                                ],
                            ],
                            'view'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action'    => 'view',
                                        'privilege' => 'view'
                                    ],
                                ],
                            ],
                            'replace'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/replace/[:id].html',
                                    'defaults' => [
                                        'action'    => 'replace',
                                        'privilege' => 'replace'
                                    ],
                                ],
                            ],
                            'download' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/download/[:id].pdf',
                                    'defaults' => [
                                        'action'    => 'download',
                                        'privilege' => 'download'
                                    ],
                                ],
                            ],
                        ]
                    ],
                ],
            ],
            'zfcadmin' => [
                'child_routes' => [
                    'program-manager' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/program-manager',
                            'defaults' => [
                                'controller' => 'program-manager',
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'new'    => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new/:entity',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/:entity/:id',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/delete/:entity/:id',
                                    'defaults' => [
                                        'action' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

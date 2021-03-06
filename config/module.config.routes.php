<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Program\Controller;

return [
    'router' => [
        'routes' => [
            'community' => [
                'child_routes' => [
                    'program' => [
                        'type'          => 'Literal',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/program',
                            'defaults' => [
                                'controller' => Controller\ProgramManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'nda' => [
                                'type'         => 'Literal',
                                'priority'     => 1000,
                                'options'      => [
                                    'route'    => '/nda',
                                    'defaults' => [
                                        'controller' => Controller\NdaController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'child_routes' => [
                                    'upload'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/upload[/call-:callId].html',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults'    => [
                                                'action'    => 'upload',
                                                'privilege' => 'upload',
                                            ],
                                        ],
                                    ],
                                    'submit'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/submit[/call-:callId].html',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults'    => [
                                                'action'    => 'submit',
                                                'privilege' => 'submit',
                                            ],
                                        ],
                                    ],
                                    'render'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/render[/call-:callId].pdf',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults'    => [
                                                'action'    => 'render',
                                                'privilege' => 'render',
                                            ],
                                        ],
                                    ],
                                    'view'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/nda-[:id].html',
                                            'defaults' => [
                                                'action'    => 'view',
                                                'privilege' => 'view',
                                            ],
                                        ],
                                    ],
                                    'replace'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/replace/nda-[:id].html',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults'    => [
                                                'action'    => 'replace',
                                                'privilege' => 'replace',
                                            ],
                                        ],
                                    ],
                                    'download' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/download/nda-[:id].[:ext]',
                                            'constraints' => [
                                                'id' => '\d+',
                                            ],
                                            'defaults'    => [
                                                'action'    => 'download',
                                                'privilege' => 'download',
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
                                        'controller' => Controller\DoaController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'child_routes' => [
                                    'render'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/render/organisation-[:organisationId]/program-[:programId].pdf',
                                            'defaults' => [
                                                'action'    => 'render',
                                                'privilege' => 'render',
                                            ],
                                        ],
                                    ],
                                    'upload'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/upload/organisation-[:organisationId]/program-[:programId].html',
                                            'defaults' => [
                                                'action'    => 'upload',
                                                'privilege' => 'upload',
                                            ],
                                        ],
                                    ],
                                    'view'     => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action'    => 'view',
                                                'privilege' => 'view',
                                            ],
                                        ],
                                    ],
                                    'replace'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/replace/[:id].html',
                                            'defaults' => [
                                                'action'    => 'replace',
                                                'privilege' => 'replace',
                                            ],
                                        ],
                                    ],
                                    'download' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/download/[:id].pdf',
                                            'defaults' => [
                                                'action'    => 'download',
                                                'privilege' => 'download',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'call'    => [
                        'type'         => 'Literal',
                        'priority'     => 1000,
                        'options'      => [
                            'route'    => '/call',
                            'defaults' => [
                                'controller' => Controller\CallController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'child_routes' => [
                            'index' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/index[/call-:call].html',
                                    'defaults' => [
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'zfcadmin'  => [
                'child_routes' => [
                    'program' => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/program',
                            'defaults' => [
                                'controller' => Controller\ProgramManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'         => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'view'        => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'export-size' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/export-size[/program-:id].html',
                                    'defaults' => [
                                        'action' => 'export-size',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'call'    => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/call',
                            'defaults' => [
                                'controller' => Controller\CallManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'              => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'view'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'size'             => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/size/call-[:id].html',
                                    'defaults' => [
                                        'action' => 'size',
                                    ],
                                ],
                            ],
                            'export-size'      => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/export-size[/call-:id].html',
                                    'defaults' => [
                                        'action' => 'export-size',
                                    ],
                                ],
                            ],
                            'funding'          => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/funding/call-[:id][/year-:year].html',
                                    'defaults' => [
                                        'action' => 'funding',
                                    ],
                                ],
                            ],
                            'download-funding' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/funding/download/call-[:id].csv',
                                    'defaults' => [
                                        'action' => 'download-funding',
                                    ],
                                ],
                            ],
                            'country'          => [
                                'type'          => 'Segment',
                                'priority'      => 1000,
                                'options'       => [
                                    'route'    => '/country',
                                    'defaults' => [
                                        'controller' => Controller\CallCountryManagerController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'new'  => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/new/call-[:call]/country-[:country].html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'nda'     => [
                        'type'          => 'Segment',
                        'priority'      => 1000,
                        'options'       => [
                            'route'    => '/nda',
                            'defaults' => [
                                'controller' => Controller\NdaManagerController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'approval' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/approval.html',
                                    'defaults' => [
                                        'action'    => 'approval',
                                        'privilege' => 'approval-admin',
                                    ],
                                ],
                            ],
                            'view'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'priority'    => 100,
                                    'route'       => '/[:id].html',
                                    'constraints' => [
                                        'id' => '[0-9_-]+',
                                    ],
                                    'defaults'    => [
                                        'action'    => 'view',
                                        'privilege' => 'view-admin',
                                    ],
                                ],
                            ],
                            'edit'     => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'constraints' => [
                                            'id' => '[0-9_-]+',
                                        ],
                                        'action'      => 'edit',
                                        'privilege'   => 'edit-admin',
                                    ],
                                ],
                            ],
                            'approve'  => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/approve.html',
                                    'defaults' => [
                                        'action'    => 'approve',
                                        'privilege' => 'edit-admin',
                                    ],
                                ],
                            ],
                            'upload'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'priority'    => 100,
                                    'route'       => '/upload/contact-[:contactId].html',
                                    'constraints' => [
                                        'contactId' => '[0-9_-]+',
                                    ],
                                    'defaults'    => [
                                        'action'    => 'upload',
                                        'privilege' => 'upload-admin',
                                    ],
                                ],
                            ],
                            'render'   => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/render/contact-[:contactId][/call-:callId].pdf',
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                    'defaults'    => [
                                        'action'    => 'render',
                                        'privilege' => 'render',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'funder'  => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'    => '/funder',
                            'defaults' => [
                                'controller' => Controller\FunderManagerController::class,
                                'action'     => 'list',
                                'page'       => 1,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'new'  => [
                                'type'     => 'Literal',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'     => 'Segment',
                                'priority' => 1000,
                                'options'  => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
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

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
return [
    'navigation' => [
        'community' => [
            'idea' => [
                'pages' => [
                    'upload-nda' => [
                        'label'  => _("txt-nav-upload-nda"),
                        'route'  => 'community/program/nda/upload',
                        'params' => [
                            'entities'   => [
                                'id' => Program\Entity\Call\Call::class
                            ],
                            'routeParam' => [
                                'id' => 'callId'
                            ],
                            'invokables' => [
                                Program\Navigation\Invokable\UploadNdaLabel::class
                            ]
                        ],
                    ],
                    'view-nda'   => [
                        'label'   => _("txt-nav-view-nda"),
                        'route'   => 'community/program/nda/view',
                        'visible' => false,
                        'params'  => [
                            'entities'   => [
                                'id' => Program\Entity\Nda::class
                            ],
                            'invokables' => [
                                Program\Navigation\Invokable\NdaLabel::class
                            ]
                        ],
                        'pages'   => [
                            'replace-nda' => [
                                'label'   => _("txt-nav-replace-nda"),
                                'route'   => 'community/program/nda/replace',
                                'visible' => false,
                                'params'  => [
                                    'entities' => [
                                        'id' => Program\Entity\Nda::class
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'admin'     => [
            // And finally, here is where we define our page hierarchy
            'project' => [
                'order' => 20,
                'pages' => [
                    'program-list'      => [
                        'label' => _("txt-nav-program-list"),
                        'route' => 'zfcadmin/program/list',
                        'pages' => [
                            'program-view' => [
                                'route'   => 'zfcadmin/program/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Program::class
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\ProgramLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/program/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Program::class
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'program-new'  => [
                                'route'   => 'zfcadmin/program/new',
                                'label'   => _("txt-add-program"),
                                'visible' => false,
                            ],
                        ]
                    ],
                    'program-call-list' => [
                        'label' => _("txt-nav-program-calls"),
                        'route' => 'zfcadmin/call/list',
                        'pages' => [
                            'program-call-view' => [
                                'route'   => 'zfcadmin/call/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Call\Call::class
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\CallLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/call/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Call\Call::class
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'call-new'          => [
                                'route'   => 'zfcadmin/call/new',
                                'label'   => _("txt-add-call"),
                                'visible' => false,
                            ],
                        ]
                    ],
                    'nda-approval'      => [
                        'label' => _("txt-nav-nda-approval"),
                        'route' => 'zfcadmin/nda/approval',
                    ],
                    'funder'            => [
                        'label' => _("txt-nav-funder-list"),
                        'route' => 'zfcadmin/funder/list',
                        'pages' => [
                            'funder-view' => [
                                'route'   => 'zfcadmin/funder/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Funder::class
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\FunderLabel::class
                                    ]
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/funder/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Funder::class
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'funder-new'  => [
                                'label'   => _("txt-add-funder"),
                                'route'   => 'zfcadmin/funder/new',
                                'visible' => false,
                                'params'  => [
                                    'entities' => [
                                        'id' => Program\Entity\Funder::class
                                    ],
                                ],
                            ],
                        ]
                    ],
                ],
            ],
        ],
    ],
];

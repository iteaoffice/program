<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

return [
    'navigation' => [
        'community2' => [
            'index' => [
                'pages' => [
                    'call' => [
                        'pages' => [
                            'submit-nda' => [
                                'order'     => 20,
                                'label'     => _('txt-nav-submit-nda'),
                                'route'     => 'community/program/nda/submit',
                                'resource'  => 'route/community/program/nda/submit',
                                'privilege' => 'submit',
                                'visible'   => false,
                                'pages'     => [
                                    'replace-nda' => [
                                        'label'   => _('txt-nav-replace-nda'),
                                        'route'   => 'community/program/nda/replace',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Nda::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'admin'      => [
            // And finally, here is where we define our page hierarchy
            'project' => [
                'order' => 20,
                'pages' => [
                    'program-list' => [
                        'label' => _('txt-nav-program-list'),
                        'route' => 'zfcadmin/program/list',
                        'pages' => [
                            'program-view' => [
                                'route'   => 'zfcadmin/program/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Program::class,
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\ProgramLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/program/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Program::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'program-new'  => [
                                'route'   => 'zfcadmin/program/new',
                                'label'   => _('txt-add-program'),
                                'visible' => false,
                            ],
                        ],
                    ],
                    'call-list'    => [
                        'label' => _('txt-nav-program-calls'),
                        'route' => 'zfcadmin/call/list',
                        'pages' => [
                            'program-call-view' => [
                                'route'   => 'zfcadmin/call/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Call\Call::class,
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\CallLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit'         => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/call/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Call\Call::class,
                                            ],
                                        ],
                                    ],
                                    'call-size'    => [
                                        'label'  => _('txt-nav-call-size'),
                                        'route'  => 'zfcadmin/call/size',
                                        'params' => [
                                            'entities' => [
                                                'id' => Program\Entity\Call\Call::class,
                                            ],
                                        ],
                                    ],
                                    'call-funding' => [
                                        'label'  => _('txt-nav-call-funding'),
                                        'route'  => 'zfcadmin/call/funding',
                                        'params' => [
                                            'entities' => [
                                                'id' => Program\Entity\Call\Call::class,
                                            ],
                                        ],
                                    ],
                                    'edit-country' => [
                                        'route'   => 'zfcadmin/call/country/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Program\Entity\Call\Country::class,
                                            ],
                                            'invokables' => [
                                                Program\Navigation\Invokable\CountryLabel::class,
                                            ],
                                        ],
                                    ],
                                    'new-country'  => [
                                        'label'   => _('txt-nav-add-country-information-to-call'),
                                        'route'   => 'zfcadmin/call/country/new',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Program\Entity\Call\Call::class,
                                            ],
                                            'routeParam' => [
                                                'id' => 'call',
                                            ],
                                        ],
                                    ],
                                    'view-country' => [
                                        'route'   => 'zfcadmin/call/country/view',
                                        'visible' => false,
                                        'params'  => [
                                            'entities'   => [
                                                'id' => Program\Entity\Call\Country::class,
                                            ],
                                            'invokables' => [
                                                Program\Navigation\Invokable\CountryLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'call-new'          => [
                                'route'   => 'zfcadmin/call/new',
                                'label'   => _('txt-add-call'),
                                'visible' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'tools'   => [
                'pages' => [
                    'nda-approval' => [
                        'label' => _('txt-nav-nda-approval'),
                        'route' => 'zfcadmin/nda/approval',
                        'pages' => [
                            'nda' => [
                                'route'   => 'zfcadmin/nda/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Nda::class,
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\NdaLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-edit-nda'),
                                        'route'   => 'zfcadmin/nda/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Nda::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            'contact' => [
                'pages' => [
                    'funder' => [
                        'label' => _('txt-nav-funder-list'),
                        'route' => 'zfcadmin/funder/list',
                        'order' => 1000,
                        'pages' => [
                            'funder-view' => [
                                'route'   => 'zfcadmin/funder/view',
                                'visible' => false,
                                'params'  => [
                                    'entities'   => [
                                        'id' => Program\Entity\Funder::class,
                                    ],
                                    'invokables' => [
                                        Program\Navigation\Invokable\FunderLabel::class,
                                    ],
                                ],
                                'pages'   => [
                                    'edit' => [
                                        'label'   => _('txt-nav-edit'),
                                        'route'   => 'zfcadmin/funder/edit',
                                        'visible' => false,
                                        'params'  => [
                                            'entities' => [
                                                'id' => Program\Entity\Funder::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'funder-new'  => [
                                'label'   => _('txt-add-funder'),
                                'route'   => 'zfcadmin/funder/new',
                                'visible' => false,
                                'params'  => [
                                    'entities' => [
                                        'id' => Program\Entity\Funder::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ]
        ],
    ],
];

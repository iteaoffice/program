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
        'admin' => [
            // And finally, here is where we define our page hierarchy
            'project' => [
                'pages' => [
                    'program-list'      => [
                        'label'  => _("txt-nav-program-list"),
                        'route'  => 'zfcadmin/program-manager/list',
                        'params' => [
                            'entity' => 'program'
                        ]
                    ],
                    'program-call-list' => [
                        'label'  => _("txt-nav-program-calls"),
                        'route'  => 'zfcadmin/program-manager/list',
                        'params' => [
                            'entity' => 'call'
                        ]
                    ],
                    'nda-approval'      => [
                        'label' => _("txt-nav-nda-approval"),
                        'route' => 'zfcadmin/nda-manager/approval',
                    ],
                    'funder'            => [
                        'label' => _("txt-nav-funder-list"),
                        'route' => 'zfcadmin/funder-manager/list',
                    ],
                ],
            ],
        ],
    ],
];

<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return [
    'navigation' => [
        'admin' => [
            // And finally, here is where we define our page hierarchy
            'project' => [
                'pages' => [
                    'program-list'      => [
                        'label'  => "txt-programs",
                        'route'  => 'zfcadmin/program-manager/list',
                        'params' => [
                            'entity' => 'program'
                        ]
                    ],
                    'program-call-list' => [
                        'label'  => "txt-program-calls",
                        'route'  => 'zfcadmin/program-manager/list',
                        'params' => [
                            'entity' => 'call'
                        ]
                    ],
                    'nda-approval'      => [
                        'label' => "txt-nda-approval",
                        'route' => 'zfcadmin/nda-manager/approval',
                    ],
                ],
            ],
        ],
    ],
];

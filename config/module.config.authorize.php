<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use BjyAuthorize\Guard\Route;
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Acl\Assertion\Nda as NdaAssertion;

return [
    'bjyauthorize' => [
        'guards' => [
            Route::class => [
                ['route' => 'zfcadmin/program/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/program/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/program/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/program/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/program/export-size', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/size', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/export-size', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/funding', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/download-funding', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/country/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/country/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/call/country/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/approval', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/edit', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/approve', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/upload', 'roles' => ['office']],
                ['route' => 'zfcadmin/nda/render', 'roles' => ['office']],
                ['route' => 'zfcadmin/funder/view', 'roles' => ['office']],
                ['route' => 'zfcadmin/funder/new', 'roles' => ['office']],
                ['route' => 'zfcadmin/funder/list', 'roles' => ['office']],
                ['route' => 'zfcadmin/funder/edit', 'roles' => ['office']],
                ['route' => 'community/program/nda/submit', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/render', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/replace', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/download', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/doa/upload', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/view', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/render', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/replace', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/download', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/call/index', 'roles' => ['user']],
            ],
        ],
    ],
];

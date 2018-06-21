<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

use Admin\Entity\Access;
use BjyAuthorize\Guard\Route;
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Acl\Assertion\Nda as NdaAssertion;

return [
    'bjyauthorize' => [
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            Route::class => [
                ['route' => 'zfcadmin/program/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/program/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/program/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/program/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/program/size', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/size', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/funding', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/download-funding', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/country/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/country/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/call/country/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/approval', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/approve', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/upload', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/nda/render', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/funder/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/funder/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/funder/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/funder/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/session/view', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/session/new', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/session/list', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'zfcadmin/session/edit', 'roles' => [Access::ACCESS_OFFICE]],
                ['route' => 'community/program/session/download-pdf', 'roles' => [strtolower(Access::ACCESS_USER)],],
                ['route' => 'community/program/session/download-spreadsheet', 'roles' => [strtolower(Access::ACCESS_USER)],],
                ['route' => 'community/program/nda/submit', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/view', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/render', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/replace', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/download', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/doa/upload', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/view', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/render', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/replace', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/download', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/call/index', 'roles' => [Access::ACCESS_USER]],
            ],
        ],
    ],
];

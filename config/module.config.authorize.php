<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2015 ITEA Office (https://itea3.org]
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
                ['route' => 'zfcadmin/program/edit', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/program/view', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/program/new', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/program/list', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/edit', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/view', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/new', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/list', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/country/edit', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/country/view', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/call/country/new', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route'     => 'zfcadmin/nda/approval',
                 'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                 'assertion' => NdaAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/nda/view',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => NdaAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/nda/edit',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => NdaAssertion::class
                ],
                [
                    'route'     => 'zfcadmin/nda/approve',
                    'roles'     => [strtolower(Access::ACCESS_OFFICE)],
                    'assertion' => NdaAssertion::class
                ],
                ['route' => 'zfcadmin/funder/view', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/funder/new', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/funder/list', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'zfcadmin/funder/edit', 'roles' => [strtolower(Access::ACCESS_OFFICE)]],
                ['route' => 'community/program/session/download', 'roles' => [strtolower(Access::ACCESS_USER)],],
                ['route' => 'community/program/nda/upload', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/view', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/render', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/replace', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/nda/download', 'roles' => [], 'assertion' => NdaAssertion::class],
                ['route' => 'community/program/doa/upload', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/view', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/render', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/replace', 'roles' => [], 'assertion' => DoaAssertion::class],
                ['route' => 'community/program/doa/download', 'roles' => [], 'assertion' => DoaAssertion::class],
            ],
        ],
    ],
];

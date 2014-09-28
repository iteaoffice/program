<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c] 2004-2014 ITEA Office (http://itea3.org]
 */
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Acl\Assertion\Nda as NdaAssertion;

return [
    'bjyauthorize' => [
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'program' => [],
            ],
        ],
        /* Currently, only controller and route guards exist
         */
        'guards'             => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => [
                [
                    'route' => 'program/view',
                    'roles' => [],
                ],
                [
                    'route'     => 'program/nda/upload',
                    'roles'     => [],
                    'assertion' => ndaAssertion::class
                ],
                [
                    'route'     => 'program/nda/view',
                    'roles'     => [],
                    'assertion' => ndaAssertion::class
                ],
                [
                    'route'     => 'program/nda/render',
                    'roles'     => [],
                    'assertion' => ndaAssertion::class
                ],
                [
                    'route'     => 'program/nda/replace',
                    'roles'     => [],
                    'assertion' => ndaAssertion::class
                ],
                [
                    'route'     => 'program/nda/download',
                    'roles'     => [],
                    'assertion' => ndaAssertion::class
                ],
                [
                    'route'     => 'program/doa/upload',
                    'roles'     => [],
                    'assertion' => doaAssertion::class
                ],
                [
                    'route'     => 'program/doa/view',
                    'roles'     => [],
                    'assertion' => doaAssertion::class
                ],
                [
                    'route'     => 'program/doa/render',
                    'roles'     => [],
                    'assertion' => doaAssertion::class
                ],
                [
                    'route'     => 'program/doa/replace',
                    'roles'     => [],
                    'assertion' => doaAssertion::class
                ],
                [
                    'route'     => 'program/doa/download',
                    'roles'     => [],
                    'assertion' => doaAssertion::class
                ],
            ],
        ],
    ],
];

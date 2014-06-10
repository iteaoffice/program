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
    'bjyauthorize' => [
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                'program' => [],
            ],
        ],
        /* rules can be specified here with the format:
         * [roles (array] , resource, [privilege (array|string], assertion]]
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers'     => [
            'BjyAuthorize\Provider\Rule\Config' => [
                'allow' => [],
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
                    'route'     => 'program/nda/upload',
                    'roles'     => ['user'],
                    'assertion' => 'program_acl_assertion_nda'
                ],
                [
                    'route'     => 'program/nda/view',
                    'roles'     => ['user'],
                    'assertion' => 'program_acl_assertion_nda'
                ],
                [
                    'route'     => 'program/nda/render',
                    'roles'     => ['user'],
                    'assertion' => 'program_acl_assertion_nda'
                ],
                [
                    'route'     => 'program/nda/replace',
                    'roles'     => ['user'],
                    'assertion' => 'program_acl_assertion_nda'
                ],
                [
                    'route'     => 'program/nda/download',
                    'roles'     => ['user'],
                    'assertion' => 'program_acl_assertion_nda'
                ],
                [
                    'route'     => 'program/doa/upload',
                    'roles'     => ['office'],
                    'assertion' => 'program_acl_assertion_doa'
                ],
                [
                    'route'     => 'program/doa/view',
                    'roles'     => ['office'],
                    'assertion' => 'program_acl_assertion_doa'
                ],
                [
                    'route'     => 'program/doa/render',
                    'roles'     => ['office'],
                    'assertion' => 'program_acl_assertion_doa'
                ],
                [
                    'route'     => 'program/doa/replace',
                    'roles'     => ['office'],
                    'assertion' => 'program_acl_assertion_doa'
                ],
                [
                    'route'     => 'program/doa/download',
                    'roles'     => ['office'],
                    'assertion' => 'program_acl_assertion_doa'
                ],
            ],
        ],
    ],
];

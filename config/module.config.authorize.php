<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
return array(
    'bjyauthorize' => array(
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'program' => array(),
            ),
        ),
        /* rules can be specified here with the format:
         * array(roles (array) , resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers'     => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(),
            ),
        ),
        /* Currently, only controller and route guards exist
         */
        'guards'             => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => array(
                array(
                    'route'     => 'program/nda/upload',
                    'roles'     => array('user'),
                    'assertion' => 'program_acl_assertion_nda'
                ),
                array(
                    'route'     => 'program/nda/view',
                    'roles'     => array('user'),
                    'assertion' => 'program_acl_assertion_nda'
                ),
                array(
                    'route'     => 'program/nda/render',
                    'roles'     => array('user'),
                    'assertion' => 'program_acl_assertion_nda'
                ),
                array(
                    'route'     => 'program/nda/replace',
                    'roles'     => array('user'),
                    'assertion' => 'program_acl_assertion_nda'
                ),
                array(
                    'route'     => 'program/nda/download',
                    'roles'     => array('user'),
                    'assertion' => 'program_acl_assertion_nda'
                ),
                array(
                    'route'     => 'program/doa/upload',
                    'roles'     => array('office'),
                    'assertion' => 'program_acl_assertion_doa'
                ),
                array(
                    'route'     => 'program/doa/view',
                    'roles'     => array('office'),
                    'assertion' => 'program_acl_assertion_doa'
                ),
                array(
                    'route'     => 'program/doa/render',
                    'roles'     => array('office'),
                    'assertion' => 'program_acl_assertion_doa'
                ),
                array(
                    'route'     => 'program/doa/replace',
                    'roles'     => array('office'),
                    'assertion' => 'program_acl_assertion_doa'
                ),
                array(
                    'route'     => 'program/doa/download',
                    'roles'     => array('office'),
                    'assertion' => 'program_acl_assertion_doa'
                ),
            ),
        ),
    ),
);

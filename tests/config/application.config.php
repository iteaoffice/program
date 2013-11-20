<?php
return array(
    'modules'                 => array(
        'DoctrineModule',
        'DoctrineORMModule',
        'General',
        'Contact',
        'Program',
        'Content',
        'Publication',
        'Press',
        'Deeplink',
        'Admin',
        'Mailing',
        'Organisation',
        'Project',
        'Invoice',
        'News',
        'Event',
        'Affiliation',
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/autoload/{,*.}{global,testing,local}.php',
        ),
        'module_paths'      => array(
            __DIR__ . '/../../src',
            __DIR__ . '/../../../vendor',
            __DIR__ . '/../../../../../module',
        ),
    ),
    'service_manager'         => array(
        'use_defaults' => true,
        'factories'    => array(),
    ),
);

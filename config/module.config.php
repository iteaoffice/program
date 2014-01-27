<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
$config = array(
    'controllers'     => array(
        'invokables' => array(
            'program'         => 'Program\Controller\ProgramController',
            'program-manager' => 'Program\Controller\ProgramManagerController',
        ),
    ),
    'view_manager'    => array(
        'template_map' => include __DIR__ . '/../template_map.php',
    ),
    'service_manager' => array(
        'factories'  => array(
            'program-assertion' => 'Program\Acl\Assertion\Program',
        ),
        'invokables' => array(
            'program_program_service'     => 'Program\Service\ProgramService',
            'program_form_service'        => 'Program\Service\FormService',
            'program_program_form_filter' => 'Program\Form\FilterCreateObject',
        )
    ),
    'doctrine'        => array(
        'driver'       => array(
            'program_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Program/Entity/'
                )
            ),
            'orm_default'               => array(
                'drivers' => array(
                    'Program\Entity' => 'program_annotation_driver',
                )
            )
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                )
            ),
        ),
    )
);

$configFiles = array(
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
);

foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}

return $config;

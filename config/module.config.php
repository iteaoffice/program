<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
$config = array(
    'controllers'     => array(
        'invokables' => array(
            'program'         => 'Program\Controller\ProgramController',
            'program-manager' => 'Program\Controller\ProgramManagerController',
        ),
    ),
    'view_helpers'    => array(
        'invokables' => array(
            'programLink'    => 'Program\View\Helper\ProgramLink',
            'callLink'       => 'Program\View\Helper\CallLink',
            'domainLink'     => 'Program\View\Helper\DomainLink',
            'technologyLink' => 'Program\View\Helper\TechnologyLink',
        )
    ),
    'service_manager' => array(
        'factories'  => array(
            'program-assertion' => 'Program\Acl\Assertion\Program',
        ),
        'invokables' => array(
            'program_program_service'     => 'Program\Service\ProgramService',
            'program_form_service'        => 'Program\Service\FormService',
            'program_program_form_filter' => 'Program\Form\FilterCreateProgram',


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

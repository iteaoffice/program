<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
use Program\Form;

return array(
    'factories' => array(
        'program_program_form'       => function ($sm) {
            return new Form\CreateProgram($sm);
        },
        'program_facility_form'      => function ($sm) {
            return new Form\CreateFacility($sm);
        },
        'program_area_form'          => function ($sm) {
            return new Form\CreateArea($sm);
        },
        'program_area2_form'         => function ($sm) {
            return new Form\CreateArea2($sm);
        },
        'program_sub_area_form'      => function ($sm) {
            return new Form\CreateSubArea($sm);
        },
        'program_oper_area_form'     => function ($sm) {
            return new Form\CreateOperArea($sm);
        },
        'program_oper_sub_area_form' => function ($sm) {
            return new Form\CreateOperSubArea($sm);
        },
        'program_message_form'       => function ($sm) {
            return new Form\CreateMessage($sm);
        },
    ),
);

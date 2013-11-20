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
use Program\Entity;

return array(
    'factories' => array(
        'program_program_form' => function ($sm) {
                return new Form\CreateObject($sm, new Entity\Program());
            },

    ),
);
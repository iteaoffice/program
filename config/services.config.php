<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
use Program\Entity;
use Program\Form;

return [
    'factories' => [
        'program_program_form' => function ($sm) {
            return new Form\CreateObject($sm, new Entity\Program());
        },
        'program_call_form'    => function ($sm) {
            return new Form\CreateObject($sm, new Entity\Call\Call());
        },
    ],
];

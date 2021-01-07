<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Program\Entity\Program;
use Laminas\InputFilter\InputFilter;

/**
 * Class ProgramFilter
 * @package Program\InputFilter
 */
final class ProgramFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'program',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Program::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'program',
                        ],
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 1,
                                'max'      => 100,
                            ],
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'number',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Program::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'number',
                        ],
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'invoiceMethod',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'cluster',
                'required' => true,
            ]
        );
        $this->add($inputFilter, 'program_entity_program');
    }
}

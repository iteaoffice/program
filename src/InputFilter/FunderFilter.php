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

use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Uri;

/**
 * Class FunderFilter
 * @package Program\InputFilter
 */
class FunderFilter extends InputFilter
{
    /**
     * FunderFilter constructor.
     */
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'     => 'infoOffice',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'ToNull'],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'infoPublic',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'ToNull'],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'website',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'ToNull'],
                ],
                'validators' => [
                    [
                        'name' => Uri::class,
                    ],
                ],
            ]
        );
        $this->add($inputFilter, 'program_entity_funder');
    }
}

<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\InputFilter\Call;

use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Callback;

/**
 * Class SessionFilter
 * @package Program\InputFilter\Call
 */
class SessionFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'quota',
                'required'   => false,
                'validators' => [
                    [
                        'name' => IsInt::class
                    ]
                ]
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'dateEnd',
                'required'   => false,
                'validators' => [
                    [
                        'name'    => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The end date should be greater than start date',
                            ],
                            'callback' => static function ($value, $context = []) {
                                $startDate = \DateTime::createFromFormat('Y-m-d H:i', $context['dateFrom']);
                                $endDate   = \DateTime::createFromFormat('Y-m-d H:i', $value);

                                return $endDate > $startDate;
                            },
                        ],
                    ],
                ],
            ]
        );
        $this->add($inputFilter, 'program_entity_call_session');
    }
}

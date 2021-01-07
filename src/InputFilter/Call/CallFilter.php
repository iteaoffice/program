<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\InputFilter\Call;

use DateTime;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Callback;
use Program\Entity\Call\Call;

/**
 * Class CallFilter
 * @package Program\InputFilter\Call
 */
class CallFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'call',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
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
                'name'       => 'projectNumberMask',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
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
                'name'     => 'instructionText',
                'required' => false,
                'filters'  => [
                    ['name' => 'ToNull'],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'poOpenDate',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'If a value for PO Open date is given, a value for PO Close date is mandatory',
                            ],
                            'callback' => function ($value, $context = []) {

                                return !(empty($context['poCloseDate']) && !empty($value));
                            },
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'poCloseDate',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'If a value for PO Close date is given, a value for PO Open date is mandatory',
                            ],
                            'callback' => function ($value, $context = []) {

                                return !(empty($context['poOpenDate']) && !empty($value));
                            },
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The PO Close date end date should be later than the PO Open Date',
                            ],
                            'callback' => function ($value, $context = []) {

                                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $context['poOpenDate']);
                                $endDate   = DateTime::createFromFormat('Y-m-d H:i:s', $value);

                                return $endDate > $startDate;
                            },
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'loiSubmissionDate',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'fppOpenDate',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'If a value for FPP Open date is given, a value for FPP Close date is mandatory',
                            ],
                            'callback' => function ($value, $context = []) {

                                return !(empty($context['fppCloseDate']) && !empty($value));
                            },
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The FPP Open date end date should be later than the PO Close Date',
                            ],
                            'callback' => function ($value, $context = []) {

                                //If the PO Close date is not given, then the validation is not needed here
                                if (empty($context['poCloseDate'])) {
                                    return true;
                                }

                                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $context['poCloseDate']);
                                $endDate   = DateTime::createFromFormat('Y-m-d H:i:s', $value);

                                return $endDate > $startDate;
                            },
                        ],
                    ]
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'doaSubmissionDate',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'fppCloseDate',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'If a value for FPP Close date is given, a value for FPP Open date is mandatory',
                            ],
                            'callback' => function ($value, $context = []) {

                                return !(empty($context['fppOpenDate']) && !empty($value));
                            },
                        ],
                    ],
                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'The FPP Close date end date should be later than the FPP Open Date',
                            ],
                            'callback' => function ($value, $context = []) {

                                $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $context['fppOpenDate']);
                                $endDate   = DateTime::createFromFormat('Y-m-d H:i:s', $value);

                                return $endDate > $startDate;
                            },
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'labelAnnouncementDate',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'DateTime',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd H:mm:ss',
                        ],
                    ],
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'call',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'cluster',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'       => 'callStages',
                'required'   => true,
                'validators' => [

                    [
                        'name'    => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'If a 2 stage call is chosen, the date for PO start and PO end has to be given',
                            ],
                            'callback' => function ($value, $context = []) {

                                if ((int)$value === Call::ONE_STAGE_CALL) {
                                    return true;
                                }

                                return !empty($context['poOpenDate']) && !empty($context['poCloseDate']);
                            },
                        ],
                    ],

                ],
            ]
        );
        $this->add($inputFilter, 'program_entity_call_call');
    }
}

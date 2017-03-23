<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Uri;

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

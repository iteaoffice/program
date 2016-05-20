<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\InputFilter\Call;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Program\Entity\Call\Call;
use Zend\InputFilter\InputFilter;

/**
 * Class ArticleFilter
 *
 * @package Content\InputFilter
 */
class CallFilter extends InputFilter
{
    /**
     * CallFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add([
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
        ]);
        $inputFilter->add([
            'name'       => 'poOpenDate',
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
            ],
        ]);
        $inputFilter->add([
            'name'       => 'poCloseDate',
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
            ],
        ]);
        $inputFilter->add([
            'name'       => 'poGraceDate',
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
        ]);
        $inputFilter->add([
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
            ],
        ]);
        $inputFilter->add([
            'name'       => 'fppGraceDate',
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
        ]);
        $inputFilter->add([
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
            ],
        ]);
        $inputFilter->add([
            'name'     => 'call',
            'required' => true,
        ]);
        $this->add($inputFilter, 'program_entity_call_call');
    }
}

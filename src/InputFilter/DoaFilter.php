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

namespace Program\InputFilter;

use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\Size;

/**
 * Class DoaFilter
 *
 * @package Program\InputFilter
 */
class DoaFilter extends InputFilter
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'     => 'dateApproved',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'dateSigned',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'branch',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'contact',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'organisation',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'program',
                'required' => false,
            ]
        );
        $fileUpload = new FileInput('file');
        $fileUpload->setRequired(true);
        $fileUpload->getValidatorChain()->attachByName(
            Size::class,
            [
                'min' => '20kB',
                'max' => '8MB',
            ]
        );
        $inputFilter->add($fileUpload);
        $this->add($inputFilter, 'program_entity_doa');
    }
}

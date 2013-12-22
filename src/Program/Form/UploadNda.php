<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class UploadNda extends Form
{
    /**
     * Construct the form
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    /**
     * Add the elements to the form
     *
     * @return $this
     */
    public function addElements()
    {

        // File Input
        $file = new Element\File('file');
        $file->setLabel(_("txt-upload-file"))
            ->setAttributes(array(
                'id' => 'file',
            ));
        $this->add($file);

        $this->add(array(
            'name'       => 'submit',
            'options'    => array(
                'help-block' => 'inline',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',

                'value' => _("txt-upload"),
            ),
        ));

        return $this;
    }

    /**
     * Create a dedicated input filter fo a NDA
     *
     * @return InputFilter\InputFilter
     */
    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $fileUpload = new InputFilter\FileInput('file');
        $fileUpload->setRequired(true);
        $fileUpload->getValidatorChain()->attachByName(
            'File\Extension',
            array(
                'extension' => 'pdf',
            )
        );
        $fileUpload->getValidatorChain()->attachByName(
            'File\MimeType',
            array(
                'application/pdf'
            )
        );
        $fileUpload->getValidatorChain()->attachByName(
            'File\Size',
            array(
                'min' => '10kB',
                'max' => '4MB',
            )
        );

        $inputFilter->add($fileUpload);

        return $inputFilter;
    }
}

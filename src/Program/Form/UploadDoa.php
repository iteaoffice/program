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

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Form
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class UploadDoa extends Form implements InputFilterProviderInterface
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'type'    => '\Zend\Form\Element\File',
                'name'    => 'file',
                'options' => array(
                    "label"      => "txt-file",
                    "help-block" => _("txt-a-doa-in-pdf-format-is-required")
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => array(
                    'class' => "btn btn-primary",
                    'value' => _("txt-upload-project-doa")
                )
            )
        );

        $this->add(
            array(
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => array(
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel")
                )
            )
        );
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'file' => array(
                'required'   => true,
                'validators' => array(
                    new Size(
                        array(
                            'min' => '5kB',
                            'max' => '8MB',
                        )
                    ),
                    new Extension(
                        array(
                            'extension' => array('pdf')
                        )
                    ),
                    new MimeType(
                        array(
                            'mimeType' => array('application/pdf')
                        )
                    )
                )
            )
        );
    }
}

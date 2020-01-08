<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Form;

use Laminas\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\Size;

/**
 * Class UploadNda
 *
 * @package Program\Form
 */
final class UploadNda extends Form\Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('action', '');
        $this->add(
            [
                'type' => Form\Element\Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'    => Form\Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-file',
                    'help-block' => _('txt-a-signed-nda-in-pdf-format-or-image-is-required'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Checkbox::class,
                'name'       => 'selfApprove',
                'options'    => [
                    'inline'     => true,
                    'help-block' => _('txt-self-approve-nda-checkbox-help-text'),
                ],
                'attributes' => [

                ]
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-upload-nda-title'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'approve',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-approve-nda-title'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'file' => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '5kB',
                            'max' => '8MB',
                        ]
                    ),
                    new Extension(
                        [
                            'extension' => ['pdf', 'jpg', 'jpeg', 'png'],
                        ]
                    ),
                ],
            ],
        ];
    }
}

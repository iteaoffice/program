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

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity\Call\Call;
use Zend\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;

/**
 * Class AdminUploadNda
 *
 * @package Program\Form
 */
final class AdminUploadNda extends Form\Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('action', '');
        $this->add(
            [
                'type'    => Form\Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => _('txt-file'),
                    'help-block' => _('txt-a-signed-nda-in-pdf-format-or-image-is-required'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Date::class,
                'name'       => 'dateSigned',
                'attributes' => [
                    'label'      => _('txt-date-signed'),
                    'help-block' => _('txt-nda-date-signed-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'call',
                'attributes' => [
                    'label' => _('txt-program-call'),
                ],
                'options'    => [
                    'object_manager'     => $entityManager,
                    'target_class'       => Call::class,
                    'display_empty_item' => true,
                    'empty_item_label'   => '-- No call specific NDA',
                    'help-block'         => _('txt-nda-program-call-help-block'),
                    'find_method'        => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'id' => 'DESC',
                            ],
                        ],
                    ],
                ],
            ]
        );
        $this->add(
            [
                'type' => Form\Element\Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'    => Form\Element\Checkbox::class,
                'name'    => 'approve',
                'options' => [
                    'label'      => 'txt-approve',
                    'help-block' => _('txt-admin-upload-nda-approve-text'),
                ],
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
            'call'       => [
                'required' => false,
            ],
            'dateSigned' => [
                'required' => false,
            ],
            'file'       => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '1kB',
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

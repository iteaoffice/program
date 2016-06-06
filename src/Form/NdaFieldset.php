<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity;
use Program\Entity\Call\Call;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Size;

/**
 * Class NdaFieldset.
 */
class NdaFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('program_entity_nda');
        $nda = new Entity\Nda();
        $doctrineHydrator = new DoctrineHydrator($entityManager, Entity\Nda::class);
        $this->setHydrator($doctrineHydrator)->setObject($nda);

        $this->add(
            [
                'type'    => '\Zend\Form\Element\Date',
                'name'    => 'dateApproved',
                'options' => [
                    "label" => "txt-date-approved",
                ],
            ]
        );

        $this->add(
            [
                'type'    => '\Zend\Form\Element\Date',
                'name'    => 'dateSigned',
                'options' => [
                    "label" => "txt-date-signed",
                ],
            ]
        );

        $this->add(
            [
                'type'    => '\Zend\Form\Element\Select',
                'name'    => 'contact',
                'options' => [
                    "label" => "txt-contact",
                ],
            ]
        );

        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'programCall',
                'attributes' => [
                    'label' => _("txt-program-call"),
                ],
                'options'    => [
                    'object_manager'     => $entityManager,
                    'target_class'       => Call::class,
                    'display_empty_item' => true,
                    'empty_item_label'   => "-- " . _("txt-not-connected-to-a-call"),
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
                'type'    => '\Zend\Form\Element\File',
                'name'    => 'file',
                'options' => [
                    "label"      => "txt-source-file",
                    "help-block" => _("txt-attachment-requirements"),
                ],
            ]
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
        return [
            'contact'      => [
                'required' => true,
            ],
            'programCall'  => [
                'required' => false,
            ],
            'dateSigned'   => [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => 'Date',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd',
                        ],
                    ],
                ],
            ],
            'dateApproved' => [
                'required'   => false,
                'validators' => [
                    [
                        'name'    => 'Date',
                        'options' => [
                            'pattern' => 'yyyy-mm-dd',
                        ],
                    ],
                ],
            ],
            'file'         => [
                'required'   => false,
                'validators' => [
                    new Size(
                        [
                            'min' => '20kB',
                            'max' => '8MB',
                        ]
                    ),
                ],
            ],
        ];
    }
}

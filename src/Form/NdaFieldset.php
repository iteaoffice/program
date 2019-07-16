<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity;
use Program\Entity\Call\Call;
use Zend\Form\Element\Date;
use Zend\Form\Element\File;
use Zend\Form\Element\Select;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class NdaFieldset
 *
 * @package Program\Form
 */
final class NdaFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager, Entity\Nda $nda)
    {
        parent::__construct('program_entity_nda');
        $doctrineHydrator = new DoctrineHydrator($entityManager, $nda);
        $this->setHydrator($doctrineHydrator)->setObject($nda);

        $this->add(
            [
                'type'    => Date::class,
                'name'    => 'dateApproved',
                'options' => [
                    'label' => 'txt-date-approved',
                ],
            ]
        );

        $this->add(
            [
                'type'    => Date::class,
                'name'    => 'dateSigned',
                'options' => [
                    'label' => 'txt-date-signed',
                ],
            ]
        );

        $this->add(
            [
                'type'    => Select::class,
                'name'    => 'contact',
                'options' => [
                    'label' => 'txt-contact',
                ],
            ]
        );

        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'programCall',
                'attributes' => [
                    'label' => _('txt-program-call'),
                ],
                'options'    => [
                    'object_manager'     => $entityManager,
                    'target_class'       => Call::class,
                    'display_empty_item' => true,
                    'empty_item_label'   => '-- ' . _('txt-not-connected-to-a-call'),
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
                'type'    => File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-source-file',
                    'help-block' => _('txt-attachment-requirements'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'contact'      => [
                'required' => true,
            ],
            'programCall'  => [
                'required' => false,
            ],
            'dateSigned'   => [
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
                'required' => false,
            ],
        ];
    }
}

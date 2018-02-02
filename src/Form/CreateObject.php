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

declare(strict_types=1);

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use Program\Entity\EntityAbstract;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class CreateObject
 * @package Program\Form
 */
class CreateObject extends Form
{
    /**
     * CreateObject constructor.
     *
     * @param EntityManager $entityManager
     * @param EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, EntityAbstract $object)
    {
        parent::__construct($object->get("underscore_entity_name"));

        /**
         * There is an option to drag the fieldset from the serviceManager,
         * We then need to check if if an factory is present,
         * If not we will use the default ObjectFieldset
         */
        $objectSpecificFieldset = __NAMESPACE__ . '\\' . $object->get('entity_name') . 'Fieldset';

        // Load a specific fieldSet when present
        if (class_exists($objectSpecificFieldset)) {
            $objectFieldset = new $objectSpecificFieldset($entityManager, $object);
        } else {
            $objectFieldset = new ObjectFieldset($entityManager, $object);
        }
        $objectFieldset->setUseAsBaseFieldset(true);
        $this->add($objectFieldset);


        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'restore',
                'attributes' => [
                    'class' => "btn btn-info",
                    'value' => _("txt-restore"),
                ],
            ]
        );
    }
}

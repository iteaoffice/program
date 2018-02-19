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
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element\EntityRadio;
use Program\Entity;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element;
use Zend\Form\Element\Radio;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;

/**
 * Class ObjectFieldset
 *
 * @package Event\Form
 */
class ObjectFieldset extends Fieldset
{
    /**
     * @param EntityManager $entityManager
     * @param Entity\EntityAbstract $object
     *
     * @todo Directly use the form generated by $builder->createForm()
     */
    public function __construct(EntityManager $entityManager, Entity\EntityAbstract $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $doctrineHydrator = new DoctrineHydrator($entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($object);
        $builder = new AnnotationBuilder();

        // createForm() already creates a proper form, so attaching its elements
        // to $this is only for backward compatibility
        $data = $builder->createForm($object);
        $this->addElements($data, $entityManager, $object, $this);
    }

    /**
     * @param Fieldset $dataFieldset
     * @param EntityManager $entityManager
     * @param object $object
     * @param Fieldset|null $baseFieldset
     */
    protected function addElements(
        Fieldset               $dataFieldset,
        EntityManager          $entityManager,
        /* object */ $object,
        Fieldset               $baseFieldset = null
    ) {
        /** @var Element $element */
        foreach ($dataFieldset->getElements() as $element) {
            $this->parseElement($element, $object, $entityManager);
            // Add only when a type is provided
            if (!array_key_exists('type', $element->getAttributes())) {
                continue;
            }

            if ($baseFieldset instanceof Fieldset) {
                $baseFieldset->add($element);
            } else {
                $dataFieldset->add($element);
            }
        }
        // Prepare the target element of a form collection
        if ($dataFieldset instanceof Element\Collection) {
            /** @var Element\Collection $dataFieldset */
            $targetFieldset = $dataFieldset->getTargetElement();
            // Collections have "container" fieldsets for their items, they must have the hydrator set too
            if ($targetFieldset instanceof FieldsetInterface) {
                $targetFieldset->setHydrator($this->getHydrator());
            }
            /** @var Fieldset $targetFieldset */
            foreach ($targetFieldset->getElements() as $element) {
                $this->parseElement($element, $targetFieldset->getObject(), $entityManager);
            }
        }

        // Add sub-fieldsets
        foreach ($dataFieldset->getFieldsets() as $subFieldset) {
            /** @var Fieldset $subFieldset */
            $subFieldset->setHydrator($this->getHydrator());
            $subFieldset->setAllowedObjectBindingClass(get_class($subFieldset->getObject()));
            $this->addElements($subFieldset, $entityManager, $subFieldset->getObject());
            $this->add($subFieldset);
        }
    }

    /**
     * @param Element $element
     * @param object $object
     * @param EntityManager $entityManager
     */
    protected function parseElement(Element $element, /* object */ $object, EntityManager $entityManager)
    {
        if (($element instanceof Radio) && (!$element instanceof EntityRadio)
            && ($object instanceof Entity\EntityAbstract)
        ) {
            $attributes = $element->getAttributes();
            $valueOptionsArray = sprintf('get%s', ucfirst($attributes['array']));

            $element->setOptions(array_merge(
                $element->getOptions(),
                ['value_options' => $object::$valueOptionsArray()]
            ));
        }

        $element->setOptions(array_merge($element->getOptions(), ['object_manager' => $entityManager]));
    }
}

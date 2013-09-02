<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Form;

use Zend\Form\Fieldset;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Element;

use Program\Entity;

/**
 * Create a form for a group
 */
class ProjectFieldset extends Fieldset
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('area');
        $area = new Entity\Area();

        $doctrineHydrator = new DoctrineHydrator($entityManager, 'Program\Entity\Area');
        $this->setHydrator($doctrineHydrator)->setObject($area);

        $builder = new AnnotationBuilder();

        /**
         * Go over the different form elements and add them to the form
         */
        foreach ($builder->createForm($area)->getElements() as $element) {
            /**
             * Go over each element to add the objectManager to the EntitySelect or EntityMultiCheckbox
             */
            if ($element instanceof Element\EntitySelect ||
                $element instanceof Element\EntityMultiCheckbox
            ) {
                $element->setOptions(
                    array(
                        'object_manager' => $entityManager
                    )
                );
            }

            //Add only when a type is provided
            if (array_key_exists('type', $element->getAttributes())) {
                $this->add($element);
            }
        }
    }
}

<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Service;

use Zend\Form\Form;

class FormService extends ServiceAbstract
{
    /**
     * @param null $className
     * @param null $entity
     * @param bool $bind
     *
     * @return array|object
     *
     * @throws \InvalidArgumentException
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = $this->getEntity($className);
        }
        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }
        $formName = 'program_' . $entity->get('underscore_entity_name') . '_form';
        $form = $this->getServiceLocator()->get($formName);
        $filterName = 'program_' . $entity->get('underscore_entity_name') . '_form_filter';
        $filter = $this->getServiceLocator()->get($filterName);
        $form->setInputFilter($filter);
        if ($bind) {
            $form->bind($entity);
        }

        return $form;
    }

    /**
     * @param       $className
     * @param null  $entity
     * @param array $data
     *
     * @return Form
     */
    public function prepare($className, $entity = null, $data = [])
    {
        $form = $this->getForm($className, $entity, true);
        $form->setData($data);

        return $form;
    }
}
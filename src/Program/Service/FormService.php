<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form;

use Program\Service\ProgramService;

class FormService implements ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\Form\Form
     */
    protected $form;
    /**
     * @var \Program\Service\ProgramService
     */
    protected $programService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param null $className
     * @param null $entity
     * @param bool $bind
     *
     * @return array|object
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!$entity) {
            $entity = $this->getProgramService()->getEntity($className);
        }

        $formName = 'program_' . $entity->get('underscore_entity_name') . '_form';
        $form     = $this->getServiceLocator()->get($formName);

        $filterName = 'program_' . $entity->get('underscore_entity_name') . '_form_filter';
        $filter     = $this->getServiceLocator()->get($filterName);

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
     * @return array|object
     */
    public function prepare($className, $entity = null, $data = array())
    {
        $form = $this->getForm($className, $entity, true);
        $form->setData($data);

        return $form;
    }

    /**
     * @param ProgramService $programService
     */
    public function setProgramService($programService)
    {
        $this->programService = $programService;
    }

    /**
     * Get programService.
     *
     * @return ProgramService.
     */
    public function getProgramService()
    {
        if (null === $this->programService) {
            $this->programService = $this->getServiceLocator()->get('program_generic_service');
        }

        return $this->programService;
    }

    /**
     * Set the service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get the service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}

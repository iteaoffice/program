<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Service;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormService implements ServiceLocatorAwareInterface
{
    /**
     * @var Form
     */
    protected $form;
    /**
     * @var ProgramService
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
     * @throws \InvalidArgumentException
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = $this->getProgramService()->getEntity($className);
        }
        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }
        $formName   = 'program_' . $entity->get('underscore_entity_name') . '_form';
        $form       = $this->getServiceLocator()->get($formName);
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
    public function prepare($className, $entity = null, $data = [])
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
            $this->setProgramService($this->getServiceLocator()->get(ProgramService::class));
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

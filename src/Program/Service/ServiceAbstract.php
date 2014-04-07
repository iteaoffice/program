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

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;

use Program\Entity\EntityAbstract;

use Program\Entity\Call\Call;
use Program\Entity\Nda;


use General\Service\GeneralService;
use Project\Service\VersionService;


/**
 * ServiceAbstract
 */
abstract class ServiceAbstract implements ServiceLocatorAwareInterface, ServiceInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var AuthenticationService;
     */
    protected $authenticationService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param      $entity
     * @param bool $toArray
     *
     * @return array
     */
    public function findAll($entity, $toArray = false)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findAll();
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return null|object
     */
    public function findEntityById($entity, $id)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->find($id);
    }

    /**
     * @param \Program\Entity\EntityAbstract $entity
     *
     * @return \Program\Entity\EntityAbstract
     */
    public function newEntity(EntityAbstract $entity)
    {
        if (method_exists($entity, 'getLastUpdateBy')) {
            $authService = $this->getServiceLocator()->get('zfcuser_auth_service');
            if ($authService->hasIdentity()) {
                $entity->setLastUpdateBy($authService->getIdentity()->getDisplayName());
            } else {
                $entity->setLastUpdateBy('guest');
            }
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param \Program\Entity\EntityAbstract $entity
     *
     * @return \Program\Entity\EntityAbstract
     */
    public function updateEntity(EntityAbstract $entity)
    {
        if (method_exists($entity, 'getLastUpdateBy')) {
            $authService = $this->getServiceLocator()->get('zfcuser_auth_service');
            if ($authService->hasIdentity()) {
                $entity->setLastUpdateBy($authService->getIdentity()->getDisplayName());
            } else {
                $entity->setLastUpdateBy('guest');
            }
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param \Program\Entity\EntityAbstract $entity
     *
     * @return bool
     */
    public function removeEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * Build dynamically a entity based on the full entity name
     *
     * @param $entity
     *
     * @return mixed
     */
    public function getEntity($entity)
    {
        $entity = $this->getFullEntityName($entity);

        return new $entity();
    }

    /**
     * Create a full path to the entity for Doctrine
     *
     * @param $entity
     *
     * @return string
     */
    public function getFullEntityName($entity)
    {
        /**
         * Convert a - to a camelCased situation
         */
        if (strpos($entity, '-') !== false) {
            $entity = explode('-', $entity);
            $entity = $entity[0] . ucfirst($entity[1]);
        }

        return ucfirst(join('', array_slice(explode('\\', __NAMESPACE__), 0, 1))) . '\\' . 'Entity' . '\\' . ucfirst(
            $entity
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ServiceAbstract
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->getServiceLocator()->get('general_general_service');
    }

    /**
     * get the version service
     *
     * @return VersionService
     */
    public function getVersionService()
    {
        return $this->getServiceLocator()->get('project_version_service');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (null === $this->authenticationService) {
            $this->authenticationService = $this->getServiceLocator()->get('zfcuser_auth_service');
        }

        return $this->authenticationService;
    }
}

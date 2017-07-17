<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Service;

use Admin\Service\AdminService;
use Affiliation\Service\AffiliationService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Program\Entity;
use Program\Entity\EntityAbstract;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceAbstract.
 */
abstract class ServiceAbstract implements ServiceInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var AffiliationService
     */
    protected $affiliationService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var VersionService;
     */
    protected $versionService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var AdminService
     */
    protected $adminService;

    /**
     * @param      $entity
     * @param bool $toArray
     *
     * @return array
     */
    public function findAll($entity, $toArray = false)
    {
        return $this->getEntityManager()->getRepository($entity)->findAll();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(): \Doctrine\ORM\EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return ServiceAbstract
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param $entity
     * @param $id
     *
     * @return null|EntityAbstract|object
     */
    public function findEntityById($entity, $id):?EntityAbstract
    {
        return $this->getEntityManager()->getRepository($entity)->find($id);
    }

    /**
     * @param string $entity
     * @param        $filter
     * @param array $ignoreFilter
     *
     * @return Query
     */
    public function findEntitiesFiltered($entity, $filter, $ignoreFilter = [])
    {
        return $this->getEntityManager()->getRepository($entity)
            ->findFiltered($filter, $ignoreFilter, AbstractQuery::HYDRATE_SIMPLEOBJECT);
    }

    /**
     * @param Entity\EntityAbstract $entity
     *
     * @return Entity\EntityAbstract
     */
    public function newEntity(EntityAbstract $entity)
    {
        return $this->updateEntity($entity);
    }

    /**
     * @param Entity\EntityAbstract $entity
     *
     * @return Entity\EntityAbstract
     */
    public function updateEntity(EntityAbstract $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param Entity\EntityAbstract $entity
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
     * @return AffiliationService
     */
    public function getAffiliationService(): AffiliationService
    {
        return $this->affiliationService;
    }

    /**
     * @param AffiliationService $affiliationService
     *
     * @return ServiceAbstract
     */
    public function setAffiliationService($affiliationService)
    {
        $this->affiliationService = $affiliationService;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService(): GeneralService
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return ServiceAbstract
     */
    public function setGeneralService($generalService)
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * @return VersionService
     */
    public function getVersionService(): VersionService
    {
        return $this->versionService;
    }

    /**
     * @param VersionService $versionService
     *
     * @return ServiceAbstract
     */
    public function setVersionService($versionService)
    {
        $this->versionService = $versionService;

        return $this;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService(): ProjectService
    {
        if (is_null($this->projectService)) {
            $this->projectService = $this->getServiceLocator()->get(ProjectService::class);
        }

        return $this->projectService;
    }

    /**
     * @param ProjectService $projectService
     *
     * @return ServiceAbstract
     */
    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator(): ServiceLocatorInterface
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface|ContainerInterface $serviceLocator
     *
     * @return ServiceAbstract
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return AdminService
     */
    public function getAdminService(): AdminService
    {
        return $this->adminService;
    }

    /**
     * @param AdminService $adminService
     * @return ServiceAbstract
     */
    public function setAdminService(AdminService $adminService): ServiceAbstract
    {
        $this->adminService = $adminService;

        return $this;
    }
}

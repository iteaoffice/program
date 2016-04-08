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

use Affiliation\Service\AffiliationService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use General\Service\GeneralService;
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
     * @param $entity
     * @param $id
     *
     * @return null|Entity\Doa|Entity\Call\Call|Entity\Nda|Entity\Program
     */
    public function findEntityById($entity, $id)
    {
        return $this->getEntityManager()->getRepository($entity)->find($id);
    }

    /**
     * @param string $entity
     * @param        $filter
     * @param array  $ignoreFilter
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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
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
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ServiceAbstract
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService()
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
    public function getGeneralService()
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
    public function getVersionService()
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
    public function getProjectService()
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
}

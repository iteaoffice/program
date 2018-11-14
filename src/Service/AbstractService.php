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

namespace Program\Service;

use Admin\Entity\Access;
use Admin\Entity\Permit;
use Admin\Repository\Permit\Role;
use Program\Entity\AbstractEntity;
use Contact\Entity;
use Contact\Service\SelectionContactService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractService
 *
 * @package Project\Service
 */
abstract class AbstractService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var SelectionContactService
     */
    protected $selectionContactService;

    public function __construct(EntityManager $entityManager, SelectionContactService $selectionContactService = null)
    {
        $this->entityManager = $entityManager;
        $this->selectionContactService = $selectionContactService;
    }

    public function findFilteredByContact(string $entity, $filter, Entity\Contact $contact): QueryBuilder
    {
        //The 'filter' should always be there to support the repositories
        if (!array_key_exists('filter', $filter)) {
            $filter['filter'] = [];
        }

        $qb = $this->findFiltered($entity, $filter);

        return $this->limitQueryBuilderByPermissions($qb, $contact, $entity);
    }

    public function findFiltered(string $entity, array $filter): QueryBuilder
    {
        return $this->entityManager->getRepository($entity)->findFiltered(
            $filter,
            AbstractQuery::HYDRATE_SIMPLEOBJECT
        );
    }

    protected function limitQueryBuilderByPermissions(
        QueryBuilder $qb,
        Entity\Contact $contact,
        string $entity,
        string $permit = 'list'
    ): QueryBuilder {

        //Create an entity from the name
        /** @var AbstractEntity $entity */
        $entity = new $entity();

        switch ($permit) {
            case 'edit':
                $limitQueryBuilder = $this->parseWherePermit($entity, 'edit', $contact);
                break;
            case 'list':
            default:
                $limitQueryBuilder = $this->parseWherePermit($entity, 'list', $contact);
                break;
        }


        /*
         * Limit the projects based on the rights
         */
        if (null !== $limitQueryBuilder) {
            $qb->andWhere(
                $qb->expr()
                    ->in(strtolower($entity->get('underscore_entity_name')), $limitQueryBuilder->getDQL())
            );
        } else {
            $qb->andWhere(
                $qb->expr()->isNull(
                    strtolower($entity->get('underscore_entity_name'))
                    . '.id'
                )
            );
        }

        return $qb;
    }

    /**
     * This function returns an queryBuilderObject which can be added to the query to parse the correct list of entities
     * This is done by selecting the keyId's from the Permit/Contact object based no a given role_id
     * The role_id is found by the given entity and role name (==string)
     *
     * @param object         $entity
     * @param string         $roleName
     * @param Entity\Contact $contact
     *
     * @return QueryBuilder|null
     */
    public function parseWherePermit(object $entity, string $roleName, Entity\Contact $contact): ?QueryBuilder
    {
        if (!\method_exists($entity, 'get')) {
            throw new \InvalidArgumentException(sprintf("Entity '%s' should at least have a 'get'", $entity));
        }

        $permitEntity = $this->findPermitEntityByEntity($entity);

        if (null === $permitEntity) {
            throw new \InvalidArgumentException(sprintf("Entity '%s' cannot be found as permit", $entity));
        }

        //Try to find the corresponding role
        $role = $this->entityManager->getRepository(Permit\Role::class)->findOneBy(
            [
                'entity' => $permitEntity,
                'role'   => $roleName,
            ]
        );


        if (null === $role) {
            //We have no roles found, so return a query which gives always zeros
            //We will simply return NULL
            print sprintf("Role '%s' on entity '%s' could not be found", $roleName, $entity);

            return null;
        }

        //@todo; fix this when no role is found (equals to NULL for example)
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('permit_contact.keyId');
        $qb->from(Permit\Contact::class, 'permit_contact');
        $qb->andWhere('permit_contact.contact = ' . $contact->getId());
        $qb->andWhere('permit_contact.role = ' . $role->getId());

        return $qb;
    }

    public function findPermitEntityByEntity(object $entity): ?Permit\Entity
    {
        return $this->entityManager->getRepository(Permit\Entity::class)
            ->findOneBy(['underscoreFullEntityName' => $entity->get('underscore_entity_name')]);
    }

    public function findAll(string $entity): array
    {
        return $this->entityManager->getRepository($entity)->findAll();
    }

    public function find(string $entity, int $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entity)->find($id);
    }

    public function findByName(string $entity, string $column, string $name): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entity)->findOneBy([$column => $name]);
    }

    public function save(AbstractEntity $entity): AbstractEntity
    {
        if (!$this->entityManager->contains($entity)) {
            $this->entityManager->persist($entity);
        }


        $this->entityManager->flush();


        $this->flushPermitsByEntityAndId($entity, (int)$entity->getId());

        return $entity;
    }

    /**
     * @param AbstractEntity $entity
     * @param int            $id
     */
    public function flushPermitsByEntityAndId(AbstractEntity $entity, int $id): void
    {
        $permitEntity = $this->findPermitEntityByEntity($entity);
        /**
         * Do not do anything when the permit cannot be found
         */
        if (null === $permitEntity) {
            return;
        }

        $repository = $this->entityManager->getRepository(Permit\Entity::class);
        $repository->flushPermitsByEntityAndId($permitEntity, $id);

        $this->flushAccessPermitsByEntityAndId($permitEntity, $id);
    }

    /**
     * @param Permit\Entity $permitEntity
     * @param int           $id
     */
    private function flushAccessPermitsByEntityAndId(Permit\Entity $permitEntity, int $id): void
    {
        /**
         * Add the role based on the role_selections
         */
        foreach ($permitEntity->getRole() as $role) {
            foreach ($role->getAccess() as $accessRole) {
                $this->flushPermitsPerRoleByAccessRoleAndId($role, $accessRole, $id);
            }
        }
    }

    /**
     * Flush the e permissions by AccessRole and PermitRole.
     *
     * An access role can have contacts or selections so we need to iterate over both
     *
     * @param Permit\Role $role
     * @param Access      $access
     * @param int         $id
     */
    private function flushPermitsPerRoleByAccessRoleAndId(Permit\Role $role, Access $access, $id): void
    {
        /** @var Role $repository */
        $repository = $this->entityManager->getRepository(Permit\Role::class);

        /*
         * Go over te contacts in the selection
         */
        foreach ($access->getContact() as $contact) {
            if (null === $contact->getDateEnd()) {
                $repository->insertPermitsForRoleByContactAndId($role, $contact, $id);
            }
        }

        /*
         * Go over the selections in having the access role
         */
        foreach ($access->getSelection() as $selection) {
            foreach ($this->selectionContactService->findContactsInSelection($selection) as $contact) {
                $repository->insertPermitsForRoleByContactAndId($role, $contact, $id);
            }
        }
    }


    /**
     * @param AbstractEntity $abstractEntity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->remove($abstractEntity);
        $this->entityManager->flush();
    }

    /**
     * @param AbstractEntity $abstractEntity
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function refresh(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->refresh($abstractEntity);
    }
}

<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (program_entity_call_call) Copyright (c) 2004-2017 ITEA Office (https://itea3.org) (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Repository\Call;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Program\Entity;
use Project\Entity\Project;
use Project\Entity\Version\Type;

/**
 * @category    Program
 */
class Call extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return QueryBuilder
     */
    public function findFiltered(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->join('program_entity_call_call.program', 'program_entity_program');

        $direction = 'DESC';
        if (isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('program_entity_call_call.id', $direction);
                break;
            case 'program':
                $queryBuilder->addOrderBy('program_entity_program.program', $direction);
                break;
            case 'po-open-date':
                $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', $direction);
                break;
            case 'po-close-date':
                $queryBuilder->addOrderBy('program_entity_call_call.poCloseDate', $direction);
                break;
            case 'fpp-open-date':
                $queryBuilder->addOrderBy('program_entity_call_call.fppOpenDate', $direction);
                break;
            case 'fpp-close-date':
                $queryBuilder->addOrderBy('program_entity_call_call.fppCloseDate', $direction);
                break;
            case 'fpp-program-date':
                $queryBuilder->addOrderBy('program_entity_program.program', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('program_entity_call_call.id', $direction);
        }

        return $queryBuilder;
    }

    /**
     * @param int $type
     *
     * @return null|Entity\Call\Call
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOpenCall(int $type): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $today = new \DateTime();
        switch ($type) {
            case Type::TYPE_PO:
                $queryBuilder->andWhere('program_entity_call_call.poOpenDate < :today')
                    ->andWhere(
                        'program_entity_call_call.poCloseDate > :today OR program_entity_call_call.poGraceDate > :today'
                    )->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :today')
                    ->andWhere(
                        'program_entity_call_call.fppCloseDate > :today OR program_entity_call_call.fppGraceDate > :today'
                    )->setParameter('today', $today);
                break;
            default:
                throw new \InvalidArgumentException(sprintf("This selected type %s is invalid", $type));
                break;
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param Entity\Program|null $program
     *
     * @return array
     */
    public function findNonEmptyAndActiveCalls(Entity\Program $program = null): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Show only calls which are already in projects
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('call.id');
        $subSelect->from(Project::class, 'project_entity_project');
        $subSelect->join('project_entity_project.call', 'call');
        $queryBuilder->andWhere($queryBuilder->expr()->in('program_entity_call_call.id', $subSelect->getDQL()));

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        if ($program !== null) {
            $queryBuilder->andWhere('program_entity_call_call.program = :program')->setParameter('program', $program);
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @param Entity\Program|null $program
     *
     * @return array
     */
    public function findActiveCalls(Entity\Program $program = null): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        if ($program !== null) {
            $queryBuilder->andWhere('program_entity_call_call.program = :program')->setParameter('program', $program);
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @return Entity\Call\Call[]
     */
    public function findWithAchievement(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->join('program_entity_call_call.project', 'project_entity_project');
        $queryBuilder->join('project_entity_project.achievement', 'project_entityachievement');

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }


    /**
     * @return array
     */
    public function findLastCallAndActiveVersionType(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $today = new \DateTime();
        $queryBuilder->where('program_entity_call_call.poOpenDate < :today')->andWhere(
            'program_entity_call_call.poCloseDate > :today OR program_entity_call_call.poGraceDate > :today'
        )
            ->setParameter('today', $today);

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        $queryBuilder->setMaxResults(1);

        /**
         * Check first if we find an open PO
         */
        if (null !== $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult()) {
            /*
             * We have found an open PO and call, return the result
             */
            return [
                'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO,
            ];
        }

        $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :today')->andWhere(
            'program_entity_call_call.fppCloseDate > :today OR program_entity_call_call.fppGraceDate > :today'
        )
            ->setParameter('today', $today);
        /*
         * Check first if we find an open FPP
         */
        if (null !== $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult()) {
            /*
             * We have found an open PO and call, return the result
             */
            return [
                'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
                'versionType' => Type::TYPE_FPP,
            ];
        }

        /*
         * Still no result? Then no period is active open, but we will no try to find if
         * We are _between_ an PO and FPP
         */
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        $queryBuilder->where('program_entity_call_call.fppOpenDate > :today')->setParameter('today', $today);
        $queryBuilder->orderBy('program_entity_call_call.fppOpenDate', 'DESC');
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);
        $queryBuilder->setMaxResults(1);

        if (null !== $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult()) {
            return [
                'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO,
            ];
        }

        /*
         * Still no result? Return the latest FPP (and reset the previous settings)
         */
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->orderBy('program_entity_call_call.fppCloseDate', 'DESC');
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);
        $queryBuilder->setMaxResults(1);

        return [
            'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
            'versionType' => Type::TYPE_FPP,
        ];
    }

    /**
     * @param Entity\Call\Call $call
     *
     * @return mixed
     */
    public function findMinAndMaxYearInCall(Entity\Call\Call $call)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $dql
            = 'SELECT
                        MIN(YEAR(project_entity_version_version.dateSubmitted)) AS minYear,
                        MAX(YEAR(project_entity_project.dateEnd)) AS maxYear
                   FROM Project\Entity\Project project_entity_project
                   JOIN project_entity_project.version project_entity_version_version
                   JOIN project_entity_project.call program_entity_call
                   WHERE program_entity_call.id = ' . $call->getId();
        $result = $this->_em->createQuery($dql)->getScalarResult();

        return array_shift($result);
    }

    /**
     * This function returns an array with three elements.
     *
     * 'partners' which contains the amount of partners
     * 'projects' which contains the amount of projects
     *
     * @param Entity\Call\Call $call
     *
     * @return array
     */
    public function findProjectAndPartners(Entity\Call\Call $call): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('COUNT(DISTINCT a.organisation) partners');
        $queryBuilder->addSelect('COUNT(DISTINCT a.project) projects');
        $queryBuilder->addSelect('SUM(program_entity_call_call.fundingEu) funding_eu');
        $queryBuilder->addSelect('SUM(program_entity_call_call.fundingNational) funding_national');
        $queryBuilder->from('Project\Entity\Cost\Cost', 'program_entity_call_call');
        $queryBuilder->join('program_entity_call_call.affiliation', 'a');
        $queryBuilder->join('a.organisation', 'o');
        $queryBuilder->join('a.project', 'p');
        $queryBuilder->join('p.call', 'pc');
        $queryBuilder->where('pc.call = ?1');
        $queryBuilder->addGroupBy('pc.call');
        $queryBuilder->addOrderBy('pc.call');
        $queryBuilder->setParameter(1, $call->getCall());

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }
}

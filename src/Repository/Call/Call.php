<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (program_entity_call_call) Copyright (c) 2019 ITEA Office (https://itea3.org) (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Repository\Call;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Year;
use Program\Entity;
use Project\Entity\Project;
use Project\Entity\Version\Type;

/**
 * @category    Program
 */
final class Call extends EntityRepository
{
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

    public function hasOpenCall(int $type): bool
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
                    ->andWhere('program_entity_call_call.poCloseDate > :today')
                    ->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :today')
                    ->andWhere('program_entity_call_call.fppCloseDate > :today')
                    ->setParameter('today', $today);
                break;
        }

        $queryBuilder->setMaxResults(1);

        return null !== $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function findOpenCall(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $today = new \DateTime();

        $queryBuilder->andWhere('program_entity_call_call.poOpenDate < :today')
            ->andWhere('program_entity_call_call.fppCloseDate > :today')
            ->setParameter('today', $today);

        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::ASC);

        $queryBuilder->setMaxResults(2);

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    public function findUpcomingCall(): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $today = new \DateTime();

        $queryBuilder->andWhere('program_entity_call_call.poOpenDate > :today')
            ->setParameter('today', $today);

        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::ASC);

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function findLastCall(): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::DESC);

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    public function findPreviousCall(Entity\Call\Call $call): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->andWhere('program_entity_call_call.poOpenDate < :poOpenDate');
        $queryBuilder->setParameter('poOpenDate', $call->getPoOpenDate());
        $queryBuilder->setMaxResults(1);
        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::DESC);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findNextCall(Entity\Call\Call $call): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->andWhere('program_entity_call_call.poOpenDate > :poOpenDate');
        $queryBuilder->setParameter('poOpenDate', $call->getPoOpenDate());
        $queryBuilder->setMaxResults(1);
        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::ASC);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

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

    public function findActiveCalls(Entity\Program $program = null): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        if ($program !== null) {
            $queryBuilder->andWhere('program_entity_call_call.program = :program')->setParameter('program', $program);
        }

        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::ASC);

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    public function findAmountOfActiveCalls(): int
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select($queryBuilder->expr()->count('program_entity_call_call'));
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        return (int)$queryBuilder->getQuery()->useQueryCache(true)->useResultCache(true)->getSingleScalarResult();
    }

    public function findAmountOfYears(): int
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call.poOpenDate');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::ASC);
        $queryBuilder->setMaxResults(1);

        /** @var \DateTime $firstPoOpen */
        $firstPoOpen = $queryBuilder->getQuery()->useQueryCache(true)->useResultCache(true)->getResult();

        if (\count($firstPoOpen) === 0) {
            return 0;
        }

        return (int)$firstPoOpen[0]['poOpenDate']->diff(new \DateTime())->y;
    }

    public function findWithAchievement(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->join('program_entity_call_call.project', 'project_entity_project');
        $queryBuilder->join('project_entity_project.achievement', 'project_entityachievement');

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }


    public function findMinAndMaxYearInCall(Entity\Call\Call $call)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', Year::class);

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

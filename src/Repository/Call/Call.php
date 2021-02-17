<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Repository\Call;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Year;
use Program\Entity;
use Project\Entity\Cost\Cost;
use Project\Entity\Version\Type;

use function in_array;

/**
 * Class Call
 * @package Program\Repository\Call
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
        if (
            isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
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
            default:
                $queryBuilder->addOrderBy('program_entity_call_call.id', $direction);
        }

        return $queryBuilder;
    }

    public function findOpenCallsForNewProject(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');

        //For 1 stage program call we look only at the FPP
        $oneStageSelect = $this->_em->createQueryBuilder();
        $oneStageSelect->select('program_entity_call_one_stage');
        $oneStageSelect->from(Entity\Call\Call::class, 'program_entity_call_one_stage');
        $oneStageSelect->andWhere('program_entity_call_one_stage.callStages = :oneStageCall');
        $oneStageSelect->andWhere('program_entity_call_one_stage.fppOpenDate <= :today');
        $oneStageSelect->andWhere('program_entity_call_one_stage.fppCloseDate > :today');

        //For 2-stage program call we look only at the PO
        $twoStageSelect = $this->_em->createQueryBuilder();
        $twoStageSelect->select('program_entity_call_two_stage');
        $twoStageSelect->from(Entity\Call\Call::class, 'program_entity_call_two_stage');
        $twoStageSelect->andWhere('program_entity_call_two_stage.callStages = :twoStageCall');
        $twoStageSelect->andWhere('program_entity_call_two_stage.poOpenDate <= :today');
        $twoStageSelect->andWhere('program_entity_call_two_stage.poCloseDate >= :today');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('program_entity_call_call', $oneStageSelect->getDQL()),
                $queryBuilder->expr()->in('program_entity_call_call', $twoStageSelect->getDQL())
            )
        );

        $today = new DateTime();
        $queryBuilder->setParameter('today', $today, Types::DATETIME_MUTABLE);
        $queryBuilder->setParameter('oneStageCall', Entity\Call\Call::ONE_STAGE_CALL);
        $queryBuilder->setParameter('twoStageCall', Entity\Call\Call::TWO_STAGE_CALL);
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findOpenCalls(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');

        //For 1 stage program call we look only at the FPP
        $oneStageSelect = $this->_em->createQueryBuilder();
        $oneStageSelect->select('program_entity_call_one_stage');
        $oneStageSelect->from(Entity\Call\Call::class, 'program_entity_call_one_stage');
        $oneStageSelect->andWhere('program_entity_call_one_stage.callStages = :oneStageCall');
        $oneStageSelect->andWhere('program_entity_call_one_stage.fppOpenDate <= :today');
        $oneStageSelect->andWhere('program_entity_call_one_stage.fppCloseDate > :today');

        //For 2-stage program call we look only at the PO
        $twoStageSelect = $this->_em->createQueryBuilder();
        $twoStageSelect->select('program_entity_call_two_stage');
        $twoStageSelect->from(Entity\Call\Call::class, 'program_entity_call_two_stage');
        $twoStageSelect->andWhere('program_entity_call_two_stage.callStages = :twoStageCall');
        $twoStageSelect->andWhere('program_entity_call_two_stage.poOpenDate <= :today');
        $twoStageSelect->andWhere('program_entity_call_two_stage.fppCloseDate >= :today');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('program_entity_call_call', $oneStageSelect->getDQL()),
                $queryBuilder->expr()->in('program_entity_call_call', $twoStageSelect->getDQL())
            )
        );

        $today = new DateTime();
        $queryBuilder->setParameter('today', $today, Types::DATETIME_MUTABLE);
        $queryBuilder->setParameter('oneStageCall', Entity\Call\Call::ONE_STAGE_CALL);
        $queryBuilder->setParameter('twoStageCall', Entity\Call\Call::TWO_STAGE_CALL);
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $queryBuilder->addOrderBy('program_entity_call_call.fppOpenDate', Criteria::ASC);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findUpcomingCalls(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        //For 1 stage program call we look only at the FPP
        $oneStageSelect = $this->_em->createQueryBuilder();
        $oneStageSelect->select('program_entity_call_one_stage');
        $oneStageSelect->from(Entity\Call\Call::class, 'program_entity_call_one_stage');
        $oneStageSelect->andWhere('program_entity_call_one_stage.callStages = :oneStageCall');
        $oneStageSelect->andWhere('program_entity_call_one_stage.fppOpenDate > :today');

        //For 2-stage program call we look only at the PO
        $twoStageSelect = $this->_em->createQueryBuilder();
        $twoStageSelect->select('program_entity_call_two_stage');
        $twoStageSelect->from(Entity\Call\Call::class, 'program_entity_call_two_stage');
        $twoStageSelect->andWhere('program_entity_call_two_stage.callStages = :twoStageCall');
        $twoStageSelect->andWhere('program_entity_call_two_stage.poOpenDate > :today');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->in('program_entity_call_call', $oneStageSelect->getDQL()),
                $queryBuilder->expr()->in('program_entity_call_call', $twoStageSelect->getDQL())
            )
        );

        $today = new DateTime();
        $queryBuilder->setParameter('today', $today, Types::DATETIME_MUTABLE);
        $queryBuilder->setParameter('oneStageCall', Entity\Call\Call::ONE_STAGE_CALL);
        $queryBuilder->setParameter('twoStageCall', Entity\Call\Call::TWO_STAGE_CALL);
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $queryBuilder->addOrderBy('program_entity_call_call.fppOpenDate', Criteria::ASC);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findLastActiveCall(): ?Entity\Call\Call
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
        $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :fppOpenDate');
        $queryBuilder->setParameter('fppOpenDate', $call->getFppOpenDate(), Types::DATETIME_MUTABLE);
        $queryBuilder->setMaxResults(1);
        $queryBuilder->addOrderBy('program_entity_call_call.poOpenDate', Criteria::DESC);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findNextCall(Entity\Call\Call $call): ?Entity\Call\Call
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');
        $queryBuilder->andWhere('program_entity_call_call.fppOpenDate > :fppOpenDate');
        $queryBuilder->setParameter('fppOpenDate', $call->getFppOpenDate(), Types::DATETIME_MUTABLE);
        $queryBuilder->setMaxResults(1);
        $queryBuilder->addOrderBy('program_entity_call_call.fppOpenDate', Criteria::ASC);

        return $queryBuilder->getQuery()->getOneOrNullResult();
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

        return (int)$queryBuilder->getQuery()->useQueryCache(true)->enableResultCache()->getSingleScalarResult();
    }

    public function findAmountOfYears(): int
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(Entity\Call\Call::class, 'program_entity_call_call');

        //Filter here on the active calls
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', Entity\Call\Call::ACTIVE);

        $queryBuilder->addOrderBy('program_entity_call_call.fppOpenDate', Criteria::ASC);
        $queryBuilder->setMaxResults(1);

        /** @var Entity\Call\Call $firstCall */
        $firstCall = $queryBuilder->getQuery()->getOneOrNullResult();

        if (null === $firstCall) {
            return 0;
        }

        //if the call has a PO, we use that date
        if ($firstCall->hasTwoStageProcess()) {
            return (int)$firstCall->getPoOpenDate()->diff(new DateTime())->y;
        }


        return (int)$firstCall->getFppOpenDate()->diff(new DateTime())->y;
    }

    public function findMinAndMaxYearInCall(Entity\Call\Call $call): string
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
        $queryBuilder->from(Cost::class, 'program_entity_call_call');
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

<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (program_entity_call_call) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Repository\Call;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Program\Entity\Call\Call as CallEntity;
use Program\Entity\Program as ProgramEntity;
use Project\Entity\Version\Type;

/**
 * @category    Program
 */
class Call extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');
        $queryBuilder->join("program_entity_call_call.program", 'p');
        //Filter here on the active calls @todo: see if this makes sense here
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('program_entity_call_call.id', $direction);
                break;
            case 'program':
                $queryBuilder->addOrderBy('p.program', $direction);
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
                $queryBuilder->addOrderBy('p.program', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('program_entity_call_call.id', $direction);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param $type
     *
     * @throws \InvalidArgumentException
     *
     * @return null|CallEntity
     */
    public function findOpenCall($type)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');
        //Filter here on the active calls @todo: see if this makes sense here

        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        $today = new \DateTime();
        switch ($type) {
            case Type::TYPE_PO:
                $queryBuilder->andWhere('program_entity_call_call.poOpenDate < :today')
                    ->andWhere('program_entity_call_call.poCloseDate > :today OR program_entity_call_call.poGraceDate > :today')->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :today')
                    ->andWhere('program_entity_call_call.fppCloseDate > :today OR program_entity_call_call.fppGraceDate > :today')->setParameter('today', $today);
                break;
            default:
                throw new \InvalidArgumentException(sprintf("This selected type %s is invalid", $type));
                break;
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }

    /**
     * @param ProgramEntity|null $program
     *
     * @return array
     */
    public function findNonEmptyCalls(ProgramEntity $program = null)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');
        //Show only calls which are already in projects
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('call.id');
        $subSelect->from('Project\Entity\Project', 'project');
        $subSelect->join('project.call', 'call');
        $queryBuilder->andWhere($queryBuilder->expr()->in('program_entity_call_call.id', $subSelect->getDQL()));

        //Filter here on the active calls @todo: see if this makes sense here
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        if ($program !== null) {
            $queryBuilder->andWhere('program_entity_call_call.program = :program')->setParameter('program', $program);
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @return array
     */
    public function findLastCallAndActiveVersionType()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_call');
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');
        $today = new \DateTime();
        $queryBuilder->where('program_entity_call_call.poOpenDate < :today')->andWhere('program_entity_call_call.poCloseDate > :today OR program_entity_call_call.poGraceDate > :today')
            ->setParameter('today', $today);
        //Filter here on the active calls @todo: see if this makes sense here
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);

        $queryBuilder->setMaxResults(1);

        /**
         * Check first if we find an open PO
         */
        if (!is_null($queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult())) {
            /*
             * We have found an open PO and call, return the result
             */
            return [
                'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO,
            ];
        }

        $queryBuilder->andWhere('program_entity_call_call.fppOpenDate < :today')->andWhere('program_entity_call_call.fppCloseDate > :today OR program_entity_call_call.fppGraceDate > :today')
            ->setParameter('today', $today);
        /*
         * Check first if we find an open FPP
         */
        if (!is_null($queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult())) {
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
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');

        $queryBuilder->where('program_entity_call_call.fppOpenDate > :today')->setParameter('today', $today);
        $queryBuilder->orderBy('program_entity_call_call.fppOpenDate', 'DESC');
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);
        $queryBuilder->setMaxResults(1);

        if (!is_null($queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult())) {
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
        $queryBuilder->from(CallEntity::class, 'program_entity_call_call');
        $queryBuilder->orderBy('program_entity_call_call.fppCloseDate', 'DESC');
        $queryBuilder->andWhere('program_entity_call_call.active = :active');
        $queryBuilder->setParameter('active', \Program\Entity\Call\Call::ACTIVE);


        return [
            'call'        => $queryBuilder->getQuery()->useQueryCache(true)->getOneOrNullResult(),
            'versionType' => Type::TYPE_FPP,
        ];
    }

    /**
     * @param CallEntity $call
     *
     * @return mixed
     */
    public function findMinAndMaxYearInCall(CallEntity $call)
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
     * @param CallEntity $call
     *
     * @return array
     */
    public function findProjectAndPartners(CallEntity $call)
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

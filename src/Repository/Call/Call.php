<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
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
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        $queryBuilder->join("c.program", 'p');

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('c.id', $direction);
                break;
            case 'po-open-date':
                $queryBuilder->addOrderBy('c.poOpenDate', $direction);
                break;
            case 'po-close-date':
                $queryBuilder->addOrderBy('c.poCloseDate', $direction);
                break;
            case 'fpp-open-date':
                $queryBuilder->addOrderBy('c.fppOpenDate', $direction);
                break;
            case 'fpp-close-date':
                $queryBuilder->addOrderBy('c.fppCloseDate', $direction);
                break;
            case 'fpp-program-date':
                $queryBuilder->addOrderBy('p.program', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('c.id', $direction);
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
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        $today = new \DateTime();
        switch ($type) {
            case Type::TYPE_PO:
                $queryBuilder->where('c.poOpenDate < :today')
                    ->andWhere('c.poCloseDate > :today OR c.poGraceDate > :today')->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $queryBuilder->where('c.fppOpenDate < :today')
                    ->andWhere('c.fppCloseDate > :today OR c.fppGraceDate > :today')->setParameter('today', $today);
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
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        //Show only calls which are already in projects
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('call.id');
        $subSelect->from('Project\Entity\Project', 'project');
        $subSelect->join('project.call', 'call');
        $queryBuilder->andWhere($queryBuilder->expr()->in('c.id', $subSelect->getDQL()));

        if ($program !== null) {
            $queryBuilder->andWhere('c.program = :program')->setParameter('program', $program);
        }

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }

    /**
     * @return array
     */
    public function findLastCallAndActiveVersionType()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        $today = new \DateTime();
        $queryBuilder->where('c.poOpenDate < :today')->andWhere('c.poCloseDate > :today OR c.poGraceDate > :today')
            ->setParameter('today', $today);

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

        $queryBuilder->where('c.fppOpenDate < :today')->andWhere('c.fppCloseDate > :today OR c.fppGraceDate > :today')
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
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');

        $queryBuilder->where('c.fppOpenDate > :today')->setParameter('today', $today);
        $queryBuilder->orderBy('c.fppOpenDate', 'DESC');
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
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        $queryBuilder->orderBy('c.fppCloseDate', 'DESC');
        $queryBuilder->setMaxResults(1);

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
                        MIN(YEAR(p.dateStartActual)) AS minYear,
                        MAX(YEAR(p.dateEndActual)) AS maxYear
                   FROM Project\Entity\Project p
                   JOIN p.call c
                   WHERE c.id = ' . $call->getId();
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
        $queryBuilder->addSelect('SUM(c.fundingEu) funding_eu');
        $queryBuilder->addSelect('SUM(c.fundingNational) funding_national');
        $queryBuilder->from('Project\Entity\Cost\Cost', 'c');
        $queryBuilder->join('c.affiliation', 'a');
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

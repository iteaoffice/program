<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Repository
 * @subpackage  Call
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Repository\Call;

use Doctrine\ORM\EntityRepository;
use Program\Entity\Call\Call as CallEntity;
use Project\Entity\Version\Type;

/**
 * @category    Program
 * @package     Repository
 */
class Call extends EntityRepository
{
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
                    ->andWhere('c.poCloseDate > :today OR c.poGraceDate > :today')
                    ->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $queryBuilder->where('c.fppOpenDate < :today')
                    ->andWhere('c.fppCloseDate > :today OR c.fppGraceDate > :today')
                    ->setParameter('today', $today);
                break;
            default:
                throw new \InvalidArgumentException(sprintf("This selected type %s is invalid", $type));
                break;
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return CallEntity[]
     */
    public function findNonEmptyCalls()
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

        return $queryBuilder->getQuery()->getResult();
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
        $queryBuilder->where('c.poOpenDate < :today')
            ->andWhere('c.poCloseDate > :today OR c.poGraceDate > :today')
            ->setParameter('today', $today);
        /**
         * Check first if we find an open PO
         */

        if (!is_null($queryBuilder->getQuery()->getOneOrNullResult())) {
            /**
             * We have found an open PO and call, return the result
             */

            return [
                'call'        => $queryBuilder->getQuery()->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO
            ];
        }

        $queryBuilder->where('c.fppOpenDate < :today')
            ->andWhere('c.fppCloseDate > :today OR c.fppGraceDate > :today')
            ->setParameter('today', $today);
        /**
         * Check first if we find an open FPP
         */
        if (!is_null($queryBuilder->getQuery()->getOneOrNullResult())) {
            /**
             * We have found an open PO and call, return the result
             */

            return [
                'call'        => $queryBuilder->getQuery()->getOneOrNullResult(),
                'versionType' => Type::TYPE_FPP
            ];
        }

        /**
         * Still no result? Then no period is active open, but we will no try to find if
         * We are _between_ an PO and FPP
         */
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');

        $queryBuilder->where('c.fppOpenDate > :today')
            ->setParameter('today', $today);
        $queryBuilder->orderBy('c.fppOpenDate', 'DESC');
        $queryBuilder->setMaxResults(1);

        if (!is_null($queryBuilder->getQuery()->getOneOrNullResult())) {
            return [
                'call'        => $queryBuilder->getQuery()->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO
            ];
        }

        /**
         * Still no result? Return the latest FPP (and reset the previous settings)
         */
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('c');
        $queryBuilder->from("Program\Entity\Call\Call", 'c');
        $queryBuilder->orderBy('c.fppCloseDate', 'DESC');
        $queryBuilder->setMaxResults(1);

        return [
            'call'        => $queryBuilder->getQuery()->getOneOrNullResult(),
            'versionType' => Type::TYPE_FPP
        ];
    }

    /**
     * This function returns an array with three elements
     *
     * 'partners' which contains the amount of partners
     * 'projects' which contains the amount of projects
     *
     * @param  CallEntity $call
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

        return $queryBuilder->getQuery()->getResult();
    }
}

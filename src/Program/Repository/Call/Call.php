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
use Project\Entity\Version\Type;
use Program\Repository\Call\Call as CallEntity;

/**
 * @category    Project
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

        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Program\Entity\Call\Call", 'c');

        $today = new \DateTime();

        switch ($type) {
            case Type::TYPE_PO:
                $qb->where('c.poOpenDate < :today')
                    ->andWhere('c.poCloseDate > :today')
                    ->setParameter('today', $today);
                break;
            case Type::TYPE_FPP:
                $qb->where('c.fppOpenDate < :today')
                    ->andWhere('c.fppCloseDate > :today')
                    ->setParameter('today', $today);
                break;
            default:
                throw new \InvalidArgumentException(sprintf("This selected type %s is invalid", $type));
                break;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function findLastCallAndActiveVersionType()
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Program\Entity\Call\Call", 'c');

        $today = new \DateTime();

        $qb->where('c.poOpenDate < :today')
            ->andWhere('c.poCloseDate > :today')
            ->setParameter('today', $today);

        /**
         * Check first if we find an open PO
         */
        if (!is_null($qb->getQuery()->getOneOrNullResult())) {
            /**
             * We have found an open PO and call, return the result
             */

            return array(
                'call'        => $qb->getQuery()->getOneOrNullResult(),
                'versionType' => Type::TYPE_PO
            );
        }

        $qb->where('c.fppOpenDate < :today')
            ->andWhere('c.fppCloseDate > :today')
            ->setParameter('today', $today);

        /**
         * Check first if we find an open FPP
         */
        if (!is_null($qb->getQuery()->getOneOrNullResult())) {
            /**
             * We have found an open PO and call, return the result
             */

            return array(
                'call'        => $qb->getQuery()->getOneOrNullResult(),
                'versionType' => Type::TYPE_FPP
            );
        }

        /**
         * Still no result? Return the latest FPP (and reset the previous settings)
         */
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from("Program\Entity\Call\Call", 'c');
        $qb->orderBy('c.fppCloseDate', 'DESC');
        $qb->setMaxResults(1);

        return array(
            'call'        => $qb->getQuery()->getOneOrNullResult(),
            'versionType' => Type::TYPE_FPP
        );
    }
}

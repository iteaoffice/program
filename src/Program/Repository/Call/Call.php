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
use Project\Entity\VersionType;
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
            case VersionType::TYPE_PO:
                $qb->where('c.poOpenDate < :today')
                    ->andWhere('c.poCloseDate > :today')
                    ->setParameter('today', $today);
                break;
            case VersionType::TYPE_FPP:
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
}
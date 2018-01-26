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

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Program\Entity\Call\Session as SessionEntity;

/**
 * @category    Program
 */
class Session extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter): Query
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s');
        $queryBuilder->from(SessionEntity::class, 's');
        $queryBuilder->innerJoin('s.call', 'c');
        $queryBuilder->innerJoin('c.program', 'p');

        // Search
        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('s.session', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        // Filter by call
        if (array_key_exists('call', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('s.call', implode($filter['call'], ', '))
            );
        }

        $direction = Criteria::DESC;
        if (isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('s.id', $direction);
                break;
            case 'session':
                $queryBuilder->addOrderBy('s.session', $direction);
                break;
            case 'call':
                $queryBuilder->addOrderBy('p.id', $direction);
                $queryBuilder->addOrderBy('c.call', $direction);
                break;
            case 'date':
                $queryBuilder->addOrderBy('s.date', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('s.id', $direction);
        }

        return $queryBuilder->getQuery();
    }
}

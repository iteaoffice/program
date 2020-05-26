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
use Program\Entity;

/**
 * Class Session
 * @package Program\Repository\Call
 */
final class Session extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_call_session');
        $queryBuilder->from(Entity\Call\Session::class, 'program_entity_call_session');
        $queryBuilder->innerJoin('program_entity_call_session.tool', 'program_entity_idea_tool');

        $queryBuilder->leftJoin('program_entity_idea_tool.call', 'program_entity_call_call');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('program_entity_call_session.session', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        // Filter by call
        if (array_key_exists('call', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('program_entity_call_call.call', implode(', ', $filter['call']))
            );
        }

        $direction = Criteria::DESC;
        if (
            isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('program_entity_call_session.id', $direction);
                break;
            case 'session':
                $queryBuilder->addOrderBy('program_entity_call_session.session', $direction);
                break;
            case 'call':
                $queryBuilder->addOrderBy('program_entity_program.id', $direction);
                $queryBuilder->addOrderBy('program_entity_call_call.call', $direction);
                break;
            case 'date-from':
                $queryBuilder->addOrderBy('program_entity_call_session.dateFrom', $direction);
                break;
            case 'date-end':
                $queryBuilder->addOrderBy('program_entity_call_session.dateEnd', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('program_entity_call_session.id', $direction);
        }

        return $queryBuilder;
    }
}

<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Program\Entity;

/**
 * Class Funder
 * @package Program\Repository
 */
class Funder extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('funder_entity_funder');
        $queryBuilder->from(Entity\Funder::class, 'funder_entity_funder');
        $queryBuilder->join('funder_entity_funder.contact', 'contact_entity_contact');
        $queryBuilder->join('funder_entity_funder.country', 'general_entity_country');

        $direction = 'DESC';
        if (
            isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        if (null !== $filter) {
            $queryBuilder = $this->applyFilter($queryBuilder, $filter);
        }

        switch ($filter['order']) {
            case 'contact':
                $queryBuilder->addOrderBy('contact_entity_contact.lastName', $direction);
                break;
            case 'country':
                $queryBuilder->addOrderBy('general_entity_country.country', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('funder_entity_funder.country', 'ASC');
        }

        return $queryBuilder;
    }

    public function applyFilter(QueryBuilder $queryBuilder, array $filter): QueryBuilder
    {
        if (! empty($filter['search'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('contact_entity_contact.firstName', ':like'),
                    $queryBuilder->expr()->like('contact_entity_contact.lastName', ':like'),
                    $queryBuilder->expr()->like('contact_entity_contact.email', ':like'),
                    $queryBuilder->expr()->like('general_entity_country.country', ':like')
                )
            );

            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }


        if (! empty($filter['showOnWebsite'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'funder_entity_funder.showOnWebsite',
                    $filter['showOnWebsite']
                )
            );
        }

        return $queryBuilder;
    }
}

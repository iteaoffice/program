<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Program\Entity;

/**
 * @category    Funder
 */
class Funder extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter): Query
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('funder_entity_funder');
        $queryBuilder->from(Entity\Funder::class, 'funder_entity_funder');
        $queryBuilder->join('funder_entity_funder.contact', 'contact_entity_contact');
        $queryBuilder->join('funder_entity_funder.country', 'general_entity_country');

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        if (!is_null($filter)) {
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

        return $queryBuilder->getQuery();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filter
     *
     * @return QueryBuilder
     */
    public function applyFilter(QueryBuilder $queryBuilder, array $filter): QueryBuilder
    {
        if (!empty($filter['search'])) {
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


        if (!empty($filter['showOnWebsite'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->in(
                'funder_entity_funder.showOnWebsite',
                $filter['showOnWebsite']
            ));
        }

        return $queryBuilder;
    }
}

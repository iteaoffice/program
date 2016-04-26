<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
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
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('funder_entity_funder');
        $queryBuilder->from(Entity\Funder::class, 'funder_entity_funder');
        $queryBuilder->join('funder_entity_funder.contact', 'contact_entity_contact');
        $queryBuilder->join('funder_entity_funder.country', 'general_entity_country');

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
        ) {
            $direction = strtoupper($filter['direction']);
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
}

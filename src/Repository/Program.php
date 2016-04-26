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
 * @category    Program
 */
class Program extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_program');
        $queryBuilder->from(Entity\Program::class, 'program_entity_program');

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $queryBuilder->addOrderBy('program_entity_program.id', $direction);
                break;
            case 'program':
                $queryBuilder->addOrderBy('program_entity_program.program', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('program_entity_program.id', $direction);
        }

        return $queryBuilder->getQuery();
    }
}

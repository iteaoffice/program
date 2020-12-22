<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Program\Entity;
use DoctrineExtensions\Query\Mysql\Year;

/**
 * Class Program
 * @package Program\Repository
 */
class Program extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('program_entity_program');
        $queryBuilder->from(Entity\Program::class, 'program_entity_program');

        $direction = 'DESC';
        if (
            isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
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

        return $queryBuilder;
    }

    public function findMinAndMaxYearInProgram(Entity\Program $program)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', Year::class);

        $dql
            = 'SELECT
                        MIN(YEAR(project_entity_version_version.dateSubmitted)) AS minYear,
                        MAX(YEAR(project_entity_project.dateEndActual)) AS maxYear
                   FROM Project\Entity\Project project_entity_project
                   JOIN project_entity_project.call program_entity_call
                   JOIN project_entity_project.version project_entity_version_version
                   JOIN program_entity_call.program program_entity_program
                   WHERE program_entity_program.id = ' . $program->getId();
        $result = $this->_em->createQuery($dql)->getScalarResult();

        return array_shift($result);
    }
}

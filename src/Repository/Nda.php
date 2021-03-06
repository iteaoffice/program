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

use Contact\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Program\Entity;

/**
 * Class Nda
 * @package Program\Repository
 */
class Nda extends EntityRepository
{
    public function findNdaByCallAndContact(Entity\Call\Call $call, Contact $contact): ?Entity\Nda
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from(Entity\Nda::class, 'program_entity_nda');
        $qb->join('program_entity_nda.call', 'call');
        $qb->andWhere($qb->expr()->in('call', [$call->getId()]));
        $qb->andWhere('program_entity_nda.contact = ?2');
        $qb->setParameter(2, $contact);
        $qb->addOrderBy('program_entity_nda.dateCreated', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findNdaByContact(Contact $contact): ?Entity\Nda
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from(Entity\Nda::class, 'program_entity_nda');
        $qb->andWhere('program_entity_nda.contact = ?2');
        $qb->addOrderBy('program_entity_nda.dateCreated', 'DESC');
        $qb->setMaxResults(1);
        $qb->setParameter(2, $contact);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findNotApprovedNda(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from(Entity\Nda::class, 'program_entity_nda');
        $qb->andWhere($qb->expr()->isNull('program_entity_nda.dateApproved'));

        $qb->addOrderBy('program_entity_nda.dateCreated', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

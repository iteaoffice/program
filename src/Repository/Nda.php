<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Program\Repository;

use Contact\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Program\Entity\Call\Call;
use Program\Repository\Nda as NdaEntity;

/**
 * @category    Program
 */
class Nda extends EntityRepository
{
    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return null|NdaEntity
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from("Program\Entity\Nda", 'program_entity_nda');
        $qb->join('program_entity_nda.call', 'call');
        $qb->andWhere($qb->expr()->in('call', [$call->getId()]));
        $qb->andWhere('program_entity_nda.contact = ?2');
        $qb->setParameter(2, $contact);
        $qb->addOrderBy('program_entity_nda.dateCreated', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Contact $contact
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNdaByContact(Contact $contact)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from("Program\Entity\Nda", 'program_entity_nda');
        $qb->andWhere('program_entity_nda.contact = ?2');
        $qb->addOrderBy('program_entity_nda.dateCreated', 'DESC');
        $qb->setMaxResults(1);
        $qb->setParameter(2, $contact);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return NdaEntity[]
     */
    public function findNotApprovedNda()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('program_entity_nda');
        $qb->from("Program\Entity\Nda", 'program_entity_nda');
        $qb->andWhere($qb->expr()->isNull('program_entity_nda.dateApproved'));

        $qb->addOrderBy('program_entity_nda.dateCreated', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

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
namespace Program\Repository;

use Contact\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Program\Entity\Call\Call;
use Program\Repository\Nda as NdaEntity;

/**
 * @category    Program
 * @package     Repository
 */
class Nda extends EntityRepository
{
    /**
     * @param Call    $call
     * @param Contact $contact
     *
     * @return null|NdaEntity
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNdaByCallAndContact(Call $call, Contact $contact)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('n');
        $qb->from("Program\Entity\Nda", 'n');
        $qb->join('n.call', 'call');
        $qb->andWhere($qb->expr()->in('call', [$call->getId()]));
        $qb->andWhere('n.contact = ?2');
        $qb->setParameter(2, $contact);
        $qb->addOrderBy('n.dateCreated', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Contact $contact
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNdaByContact(Contact $contact)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('n');
        $qb->from("Program\Entity\Nda", 'n');
        $qb->andWhere('n.contact = ?2');
        $qb->addOrderBy('n.dateCreated', 'DESC');
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
        $qb->select('n');
        $qb->from("Program\Entity\Nda", 'n');
        $qb->andWhere($qb->expr()->isNull('n.dateApproved'));

        $qb->addOrderBy('n.dateCreated', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

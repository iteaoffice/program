<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Program\Service;

use Program\Entity\EntityAbstract;

interface ServiceInterface
{
    public function getFullEntityName($entity);

    public function updateEntity(EntityAbstract $entity);

    public function newEntity(EntityAbstract $entity);

    public function getEntityManager();

    public function findAll($entity);
}

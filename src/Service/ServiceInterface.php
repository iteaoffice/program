<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\Service;

use Program\Entity\EntityAbstract;

/**
 * Interface ServiceInterface
 *
 * @package Program\Service
 */
interface ServiceInterface
{
    public function updateEntity(EntityAbstract $entity);

    public function newEntity(EntityAbstract $entity);

    public function findAll($entity);
}

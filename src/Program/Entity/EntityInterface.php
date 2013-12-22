<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Entity;

interface EntityInterface
{
    public function __get($property);

    public function __set($property, $value);
}

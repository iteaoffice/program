<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace ProgramTest\Entity;

use PHPUnit\Framework\TestCase;
use Program\Entity\AbstractEntity;
use Program\Entity\Call\Call;

/**
 * Class CallTest
 *
 * @package ProgramTest\Entity
 */
class CallTest extends TestCase
{
    /**
     *
     */
    public function testCanCreateCountry(): void
    {
        $call = new Call();
        $this->assertInstanceOf(Call::class, $call);
        $this->assertInstanceOf(AbstractEntity::class, $call);
    }
}

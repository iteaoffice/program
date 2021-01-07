<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace ProgramTest\InputFilter;

use Program\Entity;
use Program\InputFilter\ProgramFilter;
use Program\Repository;
use Testing\Util\AbstractServiceTest;
use Laminas\InputFilter\InputFilter;

/**
 * Class GeneralTest
 *
 * @package GeneralTest\Entity
 */
class ProgramFilterTest extends AbstractServiceTest
{
    /**
     *
     */
    public function testCanCreateInputFilter(): void
    {
        $contactRepositoryMock = $this->getMockBuilder(Repository\Program::class)
            ->disableOriginalConstructor()->getMock();

        $entityManager = $this->getEntityManagerMock(Entity\Program::class, $contactRepositoryMock);

        $inputFilter = new ProgramFilter($entityManager);
        $this->assertInstanceOf(ProgramFilter::class, $inputFilter);
        $this->assertInstanceOf(InputFilter::class, $inputFilter);
    }
}

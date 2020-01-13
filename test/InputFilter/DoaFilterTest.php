<?php

/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace ProgramTest\InputFilter;

use PHPUnit\Framework\TestCase;
use Program\InputFilter\DoaFilter;
use Laminas\InputFilter\InputFilter;

/**
 * Class GeneralTest
 *
 * @package GeneralTest\Entity
 */
class DoaFilterTest extends TestCase
{
    /**
     *
     */
    public function testCanCreateInputFilter(): void
    {
        $inputFilter = new DoaFilter();
        $this->assertInstanceOf(DoaFilter::class, $inputFilter);
        $this->assertInstanceOf(InputFilter::class, $inputFilter);
    }
}

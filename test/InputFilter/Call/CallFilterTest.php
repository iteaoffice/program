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

use PHPUnit\Framework\TestCase;
use Program\InputFilter\Call\CallFilter;
use Laminas\InputFilter\InputFilter;

/**
 * Class GeneralTest
 *
 * @package GeneralTest\Entity
 */
class CallFilterTest extends TestCase
{
    /**
     *
     */
    public function testCanCreateInputFilter(): void
    {
        $inputFilter = new CallFilter();
        $this->assertInstanceOf(CallFilter::class, $inputFilter);
        $this->assertInstanceOf(InputFilter::class, $inputFilter);
    }
}

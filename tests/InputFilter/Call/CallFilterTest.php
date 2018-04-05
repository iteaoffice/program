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

namespace ProgramTest\InputFilter;

use PHPUnit\Framework\TestCase;
use Program\InputFilter\Call\CallFilter;
use Zend\InputFilter\InputFilter;

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

<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

/**
 *
 */

declare(strict_types=1);

namespace Program\ValueObject;

use DateTime;

final class CallStatus
{
    private ?DateTime $referenceDate;
    private string $result;

    public function __construct(?DateTime $referenceDate, string $result)
    {
        $this->referenceDate = $referenceDate;
        $this->result        = $result;
    }

    public function getReferenceDate(): ?DateTime
    {
        return $this->referenceDate;
    }

    public function getResult(): string
    {
        return $this->result;
    }
}

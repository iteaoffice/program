<?php

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

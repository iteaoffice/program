<?php
/**
 *
 */

declare(strict_types=1);

namespace Program\ValueObject;

use Project\Entity\Version\Type;

final class CallStatus
{
    private $referenceDate;
    private $result;
    private $versionType;

    public function __construct(?\DateTime $referenceDate, string $result, ?Type $versionType)
    {
        $this->referenceDate = $referenceDate;
        $this->result = $result;
        $this->versionType = $versionType;
    }

    public function getReferenceDate(): ?\DateTime
    {
        return $this->referenceDate;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getVersionType(): ?Type
    {
        return $this->versionType;
    }
}
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

final class ProgramData
{
    private int $calls;
    private int $projects;
    private int $partners;
    private int $countries;
    private int $years;

    public function __construct(int $calls, int $projects, int $partners, int $countries, int $years)
    {
        $this->calls     = $calls;
        $this->projects  = $projects;
        $this->partners  = $partners;
        $this->countries = $countries;
        $this->years     = $years;
    }

    public function getCalls(): int
    {
        return $this->calls;
    }

    public function getProjects(): int
    {
        return $this->projects;
    }

    public function getPartners(): int
    {
        return $this->partners;
    }

    public function getCountries(): int
    {
        return $this->countries;
    }

    public function getYears(): int
    {
        return $this->years;
    }
}

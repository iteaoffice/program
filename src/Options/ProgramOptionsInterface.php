<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Options;

/**
 * Interface ProgramOptionsInterface
 *
 * @package Program\Options
 */
interface ProgramOptionsInterface
{
    public function setNdaTemplate(string $ndaTemplate);

    public function getNdaTemplate(): string;

    public function setHasNda(bool $hasNda);

    public function getHasNda(): bool;

    public function setDoaTemplate(string $doaTemplate);

    public function getDoaTemplate(): string;
}

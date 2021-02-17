<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\ValueObject;

use Doctrine\Common\Collections\ArrayCollection;
use Program\Entity\Call\Call;

final class Calls
{
    private ArrayCollection $open;
    private ArrayCollection $upcoming;

    public function __construct(array $openCalls, array $upcomingCalls)
    {
        $this->open     = new ArrayCollection($openCalls);
        $this->upcoming = new ArrayCollection($upcomingCalls);
    }

    public function isEmpty(): bool
    {
        return ! $this->hasOpen() && ! $this->hasUpcoming();
    }

    public function hasOpen(): bool
    {
        return ! $this->open->isEmpty();
    }

    public function hasUpcoming(): bool
    {
        return ! $this->upcoming->isEmpty();
    }

    public function getCallIds(): array
    {
        return array_map(static function (Call $call) {
            return (int)$call->getId();
        }, $this->toArray());
    }

    public function toArray(): array
    {
        return array_merge($this->open->toArray(), $this->upcoming->toArray());
    }
}

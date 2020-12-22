<?php

/**
 *
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

    /*
    public function getFirst(): ?Call
    {
        return $this->call1;
    }

    public function getSecond(): ?Call
    {
        return $this->call2;
    }

    public function getUpcoming(): ?Call
    {
        return $this->upcoming;
    }

    public function isEmpty(): bool
    {
        return null === $this->call1 && $this->call2 === null;
    }

    public function hasUpcoming(): bool
    {
        return null !== $this->upcoming;
    }

    public function toArray(): array
    {
        if (null === $this->call1) {
            return [];
        }

        if (! $this->isMultiple()) {
            return [$this->call1];
        }

        return [$this->call1, $this->call2];
    }

    public function isMultiple(): bool
    {
        return null !== $this->call1 && null !== $this->call2;
    }

    public function getCallIds(): array
    {
        if (null === $this->call1) {
            return [];
        }

        if (! $this->isMultiple()) {
            return [$this->call1->getId()];
        }

        return [$this->call1->getId(), $this->call2->getId()];
    }
    */
}

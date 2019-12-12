<?php
/**
 *
 */

declare(strict_types=1);

namespace Program\ValueObject;

use InvalidArgumentException;
use Program\Entity\Call\Call;

final class Calls
{
    private ? Call $call1 = null;
    private ? Call $call2 = null;
    private ? Call $upcoming = null;

    public function __construct(array $calls, ?Call $upcoming = null)
    {
        if (isset($calls[0])) {
            if (!$calls[0] instanceof Call) {
                throw new InvalidArgumentException('The object should be an instance of the Program Call');
            }

            $this->call1 = $calls[0];
        }
        if (isset($calls[1])) {
            if (!$calls[0] instanceof Call) {
                throw new InvalidArgumentException('The object should be an instance of the Program Call');
            }

            $this->call2 = $calls[1];
        }

        $this->upcoming = $upcoming;
    }

    public function getFirst() : ?Call
    {
        return $this->call1;
    }

    public function getSecond() : ?Call
    {
        return $this->call2;
    }

    public function getUpcoming() : ?Call
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

    /**
     * @return Call[]
     */
    public function toArray(): array
    {
        if (null === $this->call1) {
            return [];
        }

        if (!$this->isMultiple()) {
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

        if (!$this->isMultiple()) {
            return [$this->call1->getId()];
        }

        return [$this->call1->getId(), $this->call2->getId()];
    }
}

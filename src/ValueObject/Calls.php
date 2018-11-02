<?php
/**
 *
 */

declare(strict_types=1);

namespace Program\ValueObject;

use Program\Entity\Call\Call;

final class Calls
{
    /** @var Call */
    private $call1;
    /** @var Call */
    private $call2;

    public function __construct(array $calls)
    {
        if (isset($calls[0])) {
            if (!$calls[0] instanceof Call) {
                throw new \InvalidArgumentException('The object should be an instance of the Program Call');
            }

            $this->call1 = $calls[0];
        }
        if (isset($calls[1])) {
            if (!$calls[0] instanceof Call) {
                throw new \InvalidArgumentException('The object should be an instance of the Program Call');
            }

            $this->call2 = $calls[1];
        }
    }

    public function getFirst(): ?Call
    {
        return $this->call1;
    }

    public function getSecond(): ?Call
    {
        return $this->call2;
    }

    /**
     * @return Call[]
     */
    public function toArray(): array
    {
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
        if (!$this->isMultiple()) {
            return [$this->call1->getId()];
        }

        return [$this->call1->getId(), $this->call2->getId()];
    }
}

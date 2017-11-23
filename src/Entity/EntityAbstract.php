<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\Entity;

/**
 * Annotations class.
 *
 * @author  Johan van der Heide <johan.van.der.heide@itea3.org>
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return sprintf('%s:%s', $this->get('full_entity_name'), $this->getId());
    }

    /**
     * @param $switch
     *
     * @return null|string
     */
    public function get($switch): ?string
    {
        switch ($switch) {
            case 'full_entity_name':
                return str_replace('DoctrineORMModule\Proxy\__CG__\\', '', static::class);
            case 'entity_name':
                return str_replace(__NAMESPACE__ . '\\', '', $this->get('full_entity_name'));
            case 'underscore_entity_name':
                return strtolower(str_replace('\\', '_', $this->get('full_entity_name')));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)sprintf('%s:%s', $this->get('full_entity_name'), $this->getId());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return \is_null($this->getId());
    }

    /**
     * @param string $prop
     *
     * @return bool
     */
    public function has($prop): bool
    {
        $getter = 'get' . ucfirst($prop);
        if (method_exists($this, $getter)) {
            if (strpos($prop, 's') === 0 && is_array($this->$getter())) {
                return true;
            } elseif ($this->$getter()) {
                return true;
            }
        }

        return false;
    }
}

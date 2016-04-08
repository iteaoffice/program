<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

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
    public function getResourceId()
    {
        return sprintf("%s:%s", $this->get("full_entity_name"), $this->getId());
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return (string)sprintf("%s:%s", $this->get("full_entity_name"), $this->getId());
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->getId());
    }

    /**
     * @param string $prop
     *
     * @return bool
     */
    public function has($prop)
    {
        $getter = 'get' . ucfirst($prop);
        if (method_exists($this, $getter)) {
            if ('s' === substr($prop, 0, -1) && is_array($this->$getter())) {
                return true;
            } elseif ($this->$getter()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $switch
     *
     * @return null|string
     */
    public function get($switch)
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
}

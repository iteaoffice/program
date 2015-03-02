<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Project
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
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
     * @param $prop
     *
     * @return bool
     */
    public function has($prop)
    {
        $getter = 'get'.ucfirst($prop);
        if (method_exists($this, $getter)) {
            if ('s' === substr($prop, 0, -1) && is_array($this->$getter())) {
                return true;
            } elseif ($this->$getter()) {
                return true;
            }
        }
    }

    /**
     * @param string $switch
     *
     * @return mixed|string
     */
    public function get($switch)
    {
        switch ($switch) {
            case 'entity_name':
                return implode('', array_slice(explode('\\', get_class($this)), -1));
            case 'dashed_entity_name':
                $dash = function ($m) {
                    return '-'.strtolower($m[1]);
                };

                return preg_replace_callback('/([A-Z])/', $dash, lcfirst($this->get('entity_name')));
            case 'underscore_entity_name':
                $underscore = function ($m) {
                    return '_'.strtolower($m[1]);
                };

                return preg_replace_callback('/([A-Z])/', $underscore, lcfirst($this->get('entity_name')));
            case 'underscore_full_entity_name':
                return strtolower(str_replace('\\', '_', get_class($this)));
        }
    }
}

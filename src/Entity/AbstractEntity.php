<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Entity;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Class AbstractEntity
 *
 * @package Program\Entity
 */
abstract class AbstractEntity implements EntityInterface, ResourceInterface
{
    public function getResourceId(): string
    {
        return sprintf('%s:%s', $this->get('full_entity_name'), $this->getId());
    }

    public function get($switch): string
    {
        switch ($switch) {
            case 'class_name':
                return str_replace('DoctrineORMModule\Proxy\__CG__\\', '', static::class);
            case 'entity_name':
                return implode('', \array_slice(explode('\\', $this->get('class_name')), -1));
            case 'full_entity_name':
                return implode('', explode('\\', $this->get('class_name')));
            case 'underscore_entity_name':
                return strtolower(implode('_', explode('\\', $this->get('class_name'))));
            case 'entity_fieldset_name':
                return sprintf(
                    "%sFieldset",
                    str_replace('Entity\\', 'Form\\', $this->get('class_name'))
                ); //Run\Form\RunFieldset
            case 'entity_form_name':
                return sprintf(
                    "%sForm",
                    str_replace('Entity\\', 'Form\\', $this->get('class_name'))
                ); //Run\Form\RunForm
            case 'entity_inputfilter_name':
                return sprintf(
                    "%sFilter",
                    str_replace('Entity\\', 'InputFilter\\', $this->get('class_name'))
                ); //Run\InputFilter\RunFilter
            case 'entity_assertion_name':
                return sprintf(
                    "%s",
                    str_replace('Entity', 'Acl\\Assertion', $this->get('class_name'))
                ); //Run\Acl\Assertion\Run
            default:
                throw new \InvalidArgumentException(sprintf("Unknown option %s for get entity name", $switch));
        }
    }

    public function __toString(): string
    {
        return sprintf('%s:%s', $this->get('full_entity_name'), $this->getId());
    }

    public function isEmpty(): bool
    {
        return null === $this->getId();
    }
}

<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/Program for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class InputFilterFactory
 *
 * @package Program\Factory
 */
final class InputFilterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): InputFilter
    {
        return new $requestedName($container->get(EntityManager::class));
    }
}

<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\View\Factory;

use Interop\Container\ContainerInterface;
use Program\View\Helper\AbstractViewHelper;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\HelperPluginManager;

/**
 * Class LinkInvokableFactory
 *
 * @package Content\View\Factory
 */
final class ViewHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractViewHelper
    {
        /** @var AbstractViewHelper $viewHelper */
        $viewHelper = new $requestedName($options);
        $viewHelper->setServiceManager($container);
        $viewHelper->setHelperPluginManager($container->get('ViewHelperManager'));

        return $viewHelper;
    }
}
